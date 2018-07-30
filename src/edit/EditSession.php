<?php

namespace edit;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

use edit\blocks\BaseBlock;
use edit\blocks\BlockType;
use edit\command\tool\BrushTool;
use edit\command\tool\brush\SmoothBrush;
use edit\extent\Extent;
use edit\extent\buffer\ForgetfulExtentBuffer;
use edit\functions\block\BlockReplace;
use edit\functions\mask\Mask;
use edit\functions\mask\Masks;
use edit\functions\mask\ExistingBlockMask;
use edit\functions\mask\RegionMask;
use edit\functions\operation\Operation;
use edit\functions\operation\OperationQueue;
use edit\functions\operation\Operations;
use edit\functions\operation\ForwardExtentCopy;
use edit\functions\pattern\BlockPattern;
use edit\functions\pattern\Pattern;
use edit\functions\util\RegionOffset;
use edit\functions\visitor\LayerVisitor;
use edit\functions\visitor\RegionVisitor;
use edit\functions\RegionMaskingFilter;
use edit\functions\GroundFunction;
use edit\history\ChangeMemory;
use edit\history\change\BlockChange;
use edit\history\change\EntityCreate;
use edit\math\transform\AffineTransform;
use edit\regions\selector\CuboidRegionSelector;
use edit\regions\RegionSelector;
use edit\regions\Region;
use edit\session\ClipboardHolder;
use edit\util\Location;

class EditSession implements Extent{

	public $player;

	public $mask;
	public $clipboard;

	public $reorder = [];
	public $reorderUndo = [];

	public $changeMemory;

	public $history = [];
	public $historyPointer = -1;
	public $historyMax = -1;

	public function __construct($player){
		$this->player = $player;
		$this->regionSelector = new CuboidRegionSelector($player->getLevel());
		$this->mask = Masks::alwaysTrue();
		$this->changeMemory = new ChangeMemory();
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function setPlayer(Player $player){
		$this->player = $player;
	}

	public function getWorld() : Level{
		return $this->player->getLevel();
	}

	public function getRegionSelector(Level $world) : RegionSelector{
		return $this->regionSelector;
	}

	public function setRegionSelector(Level $world, RegionSelector $selector){
		$this->regionSelector = $selector;
	}

	public function getBlock(Vector $pt) : BaseBlock{
		$block = $this->player->getLevel()->getBlock(new Vector3($pt->getX(), $pt->getY(), $pt->getZ()));
		return new BaseBlock($block->getId(), $block->getDamage());
	}

	public function getClipboard() : ?ClipboardHolder{
		if($this->clipboard == null) return null;
		return $this->clipboard;
	}

	public function setClipboard(ClipboardHolder $clipboard){
		$this->clipboard = $clipboard;
	}

	public function getPlacementPosition(Player $player) : Vector{
		$v =  new Vector($player->x, $player->y, $player->z);
		return $v->floor();
	}

	public function remember(){
		$this->reorder();
		$this->historyPointer++;
		$this->historyMax++;
		$this->history[$this->historyPointer] = $this->changeMemory;
		foreach($this->history as $k => $h){
			if($k > $this->historyPointer) unset($this->history[$k]);
		}
		$this->changeMemory = new ChangeMemory();
	}

	public function undo(){
		if($this->historyPointer <= -1) return;
		$this->history[$this->historyPointer]->undo($this);
		$this->reorderUndo();
		$this->historyPointer--;
	}

	public function redo(){
		if(count($this->history) <= $this->historyPointer + 1) return;
		$this->history[$this->historyPointer + 1]->redo($this);
		$this->reorderUndo();
		$this->historyPointer++;
	}

	public function getMinimumPoint() : Vector{
		return new Vector(0, 0, 0);
	}

	public function getMaximumPoint() : Vector{
		return new Vector(0, 0, 0);
	}

	public function createEntity(Location $location, Entity $entity) : ?Entity{
		$skin = $entity->getSkin();
		$nbt = new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $location->getX()),
				new DoubleTag("", $location->getY()),
				new DoubleTag("", $location->getZ())
			]),
			new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			new ListTag("Rotation", [
				new FloatTag("", $location->getYaw()),
				new FloatTag("", $location->getPitch())
			]),
			"Skin" => new CompoundTag("Skin", [
				new StringTag("geometryData", $skin->getGeometryData()),
				new StringTag("geometryName", $skin->getGeometryName()),
				new StringTag("capeData", $skin->getCapeData()),
				new StringTag("Data", $skin->getSkinData()),
				new StringTag("Name", $skin->getSkinId())
			]),
		]);

		$newEntity = Entity::createEntity($entity->getSaveId(), $this->player->getLevel(), $nbt);
		for($i = 0;$i < 100;$i++){
			$data = $entity->getDataProperty($i);
			$type = $entity->getDataPropertyType($i);
			if($data !== null && $type !== null) $newEntity->setDataProperty($i, $type, $data);
		}
		for($i = 0;$i < 50;$i++){
			$data = $entity->getGenericFlag($i);
			if($data !== null) $newEntity->setGenericFlag($i, $data);
		}
		//$newEntity->setSkin($entity->getSkin());
		$newEntity->spawnToAll();
		$this->changeMemory->add(new EntityCreate($location, $newEntity));
		return $newEntity;
	}

	public function getEntities(?Region $region = null) : array{
		$entities = $this->player->getLevel()->getEntities();
		if($region === null) return $entities;
		$result = [];
		foreach($entities as $entity){
			$pt = new Vector($entity->x, $entity->y, $entity->z);
			if($region->contains($pt)) $result[] = $entity;
		}
		return $result;
	}

	public function commit() : ?Operation{
		return null;
	}

	public function reorderUndo(){
		foreach($this->reorderUndo as $o){
			$this->player->getLevel()->setBlock($o[0]->toVector3(), Block::get($o[1]->getType(), $o[1]->getData()));
		}
		$this->reorderUndo = [];
	}

	public function setBlockUndo(Vector $pt, BaseBlock $block) : bool{
		if(BlockType::shouldPlaceLast($block->getType()) || BlockType::shouldPlaceFinal($block->getType())){
			$this->reorderUndo[] = [$pt, $block];
			return true;
		}
		$this->player->getLevel()->setBlock($pt->toVector3(), Block::get($block->getType(), $block->getData()));
		return true;
	}

	public function reorder(){
		foreach($this->reorder as $o){
			$this->player->getLevel()->setBlock($o[0]->toVector3(), Block::get($o[1]->getType(), $o[1]->getData()));
		}
		$this->reorder = [];
	}

	public function setBlock(Vector $pt, $pattern) : bool{
		if(!$this->mask->test($pt)) return false;
		if($pattern instanceof Pattern) $pattern = $pattern->apply($pt);
		$this->changeMemory->add(new BlockChange($pt->toBlockVector(), $this->getBlock($pt), $pattern));
		if(BlockType::shouldPlaceLast($pattern->getType()) || BlockType::shouldPlaceFinal($pattern->getType())){
			$this->reorder[] = [$pt, $pattern];
			return true;
		}
		$this->player->getLevel()->setBlock($pt->toVector3(), Block::get($pattern->getType(), $pattern->getData()));
		return true;
	}

	public function setBlocks(array $vset, Pattern $pattern) : int{
		$affected = 0;
		foreach($vset as $v){
			$affected += $this->setBlock($v, $pattern) ? 1 : 0;
		}
		return $affected;
	}

	public function getMask() : Mask{
		return $this->mask;
	}

	public function setMask(Mask $mask){
		$this->mask = $mask;
	}

	public function getHighestTerrainBlock($x, $z, $minY, $maxY, $naturalOnly = false) : int{
		for($y = $maxY;$y >= $minY;$y--){
			$pt = new Vector($x, $y, $z);
			$block = $this->getBlock($pt);
			if($naturalOnly ? BlockType::isNaturalTerrainBlock($block->getId(), $block->getData()) : !BlockType::canPassThrough($block->getId(), $block->getData())){
				return $y;
			}
		}

		return $minY;
	}

	public function countBlock($region, $searchIDs){
		$passOn = [];
		foreach($searchIDs as $i){
			$passOn[] = new BaseBlock($i, -1);
		}
		return $this->countBlocks($region, $passOn);
	}

	public function countBlocks($region, $searchBlocks){
		$mask = new FuzzyBlockMask($this, $searchBlocks);
		$count = new Counter();
		$filter = new RegionMaskingFilter($mask, $count);
		$visitor = new RegionVisitor($region, $filter);
		Operations::completeBlindly($visitor);
		return $count->getCount();
	}

	public function fillXZ($origin, $pattern, $radius, $depth, $recursive){
		if($pattern instanceof BaseBlock){
			$pattern = new SingleBlockPattern($pattern);
		}
		$mask = new MaskIntersection(
			new RegionMask(new EllipsoidRegionnull, $origin, new Vector($radius, $radius, $radius)),
			new BoundedHeightMask(
				max($origin->getBlockY() - $depth + 1, 0),
				min(255, $origin->getBlockY())),
			Masks::negate(new ExistingBlockMask($this)));

		$replace = new BlockReplace($this, Patterns::warp($pattern));

		if($recursive){
			$visitor = new RecursiveVisitor($mask, $replace);
		}else{
			$visitor = new DownwardVisitor($mask, $replace, $origin->getBlockY());
		}

		$visitor->visit($origin);

		Operations::completeLegacy($visitor);

		return $visitor->getAffected();
	}

	public function removeAbove($position, $apothem, $height){
		$region = new CuboidRegion(
			$this->getWorld(),
			$position->add(-$apothem + 1, 0, -$apothem + 1),
			$position->add($apothem - 1, $height - 1, $apothem - 1));
		$pattern = new SingleBlockPattern(new BaseBlock(Block::AIR));
		return $this->setBlock($region, $pattern);
	}

	public function removeBelow($position, $apothem, $height){
		$region = new CuboidRegion(
			$this->getWorld(),
			$position->add(-$apothem + 1, 0, -$apothem + 1),
			$position->add($apothem - 1, -$height + 1, $apothem - 1));
		$pattern = new SingleBlockPattern(new BaseBlock(Block::AIR));
		return $this->setBlock($region, $pattern);
	}

	public function removeNear($position, $blockType, $apothem){
		$mask = new FuzzyBlockMask($this, new BaseBlock($blockType, -1));
		$a = new Vector(1, 1, 1);
		$adjustment = $a->multiply($apothem - 1);
		$region = new CuboidRegion(
			$this->getWorld(),
			$position->add($adjustment->multiply(-1)),
			$position->add($adjustment));
		$pattern = new SingleBlockPattern(new BaseBlock(Block::AIR));
		return $this->replaceBlock($region, $mask, $pattern);
	}

	public function replaceBlocks($region, $mask, $pattern){
		$replace = new BlockReplace($this, $pattern);
		$filter = new RegionMaskingFilter($mask, $replace);
		$visitor = new RegionVisitor($region, $filter);
		Operations::completeLegacy($visitor);
		return $visitor->getAffected();
	}

	public function center($region, $pattern){
		$center = $region->getCenter();
		$centerRegion = new CuboidRegion(
			$this->getWorld(),
			new Vector(((int) $center->getX()), ((int) $center->getY()), ((int) $center->getZ())),
			new Vector(MathUtils::roundHalfUp($center->getX()),
					$center->getY(), MathUtils::roundHalfUp($center->getZ())));
		return $this->setBlocks($centerRegion, $pattern);
	}

	public function makeCuboidFaces($region, $pattern){
		if($pattern instanceof BaseBlock){
			$pattern = new SingleBlockPattern($pattern);
		}

		$cuboid = CuboidRegion::makeCuboid($region);
		$faces = $cuboid->getFaces();
		return $this->setBlocks($faces, $pattern);
	}

	public function makeFaces($region, $pattern){
		if($region instanceof CuboidRegion){
			return $this->makeCuboidFaces($region, $pattern);
		}else{
			$shape = new RegionShape($region);
			return $shape->generate($this, $pattern, true);
		}
	}

	public function makeCuboidWalls($region, $pattern){
		if($pattern instanceof BaseBlock){
			$pattern = new SingleBlockPattern($pattern);
		}

		$cuboid = CuboidRegion::makeCuboid($region);
		$faces = $cuboid->getWalls();
		return $this->setBlocks($faces, $pattern);
	}

	public function makeWalls($region, $pattern){
		if($region instanceof CuboidRegion){
			return $this->makeCuboidWalls($region, $pattern);
		}else{
			$minY = $region->getMinimumPoint()->getBlockY();
			$maxY = $region->getMaximumPoint()->getBlockY();
			return $shape->generate($this, $pattern, true);
		}
	}

	public function overlayCuboidBlocks($region, $pattern){
		$replace = new BlockReplace($this, $pattern);
		$offset = new RegionOffset(new Vector(0, 1, 0), $replace);
		$ground = new GroundFunction(new ExistingBlockMask($this), $offset);
		$visitor = new LayerVisitor($region, $region->getMinimumY(), $region->getMaximumY(), $ground);
		Operations::completeLegacy($visitor);
		return $ground->getAffected();
	}

	public function naturalizeCuboidBlocks($region){
		$naturalizer = new Naturalizer($this);
		$flatRegion = Regions::asFlatRegion($region);
		$visitor = new LayerVisitor($flatRegion, $this->minimumBlockY($region), $this->maximumBlockY($region), $naturalizer);
		Operations::completeLegacy($visitor);
		return $naturalizer->getAffected();
	}

	public function stackCuboidRegion($region, $dir, $count, $copyAir){
		$size = $region->getMaximumPoint()->subtract($region->getMinimumPoint())->add(1, 1, 1);
		$to = $region->getMinimumPoint();
		$copy = new ForwardExtentCopy($this, $region, $this->getMinimumPoint(), $to);
		$copy->setRepetitions($count);
		$aff = new AffineTransform();
		$copy->setTransform($aff->translate($dir->multiply($size)));
		if(!$copyAir){
			$copy->setSourceMask(new ExistingBlockMask($this));
		}
		Operations::completeLegacy($copy);
		return $copy->getAffected();
	}

	public function moveRegion($region, $dir, $distance, $copyAir, $replacement){
		$to = $region->getMinimumPoint();

		$pattern = $replacement != null ?
				new BlockPattern($replacement) :
				new BlockPattern(new BaseBlock(Block::AIR));
		$remove = new BlockReplace($this, $pattern);

		$buffer = new ForgetfulExtentBuffer($this, new RegionMask($region));
		$copy = new ForwardExtentCopy($this, $region, $region->getMinimumPoint(), $buffer, $to);
		$aff = new AffineTransform();
		$copy->setTransform($aff->translate($dir->multiply((float)$distance)));
		$copy->setSourceFunction($remove);
		$copy->setRemovingEntities(true);
		if(!$copyAir){
			$copy->setSourceMask(new ExistingBlockMask($this));
		}

		$replace = new BlockReplace($this, $buffer);
		$visitor = new RegionVisitor($buffer->asRegion(), $replace);

		$operation = new OperationQueue([$copy, $visitor]);
		Operations::completeLegacy($operation);

		return $copy->getAffected();
	}

	public function moveCuboidRegion($region, $dir, $distance, $copyAir, $replacement){
		return $this->moveRegion($region, $dir, $distance, $copyAir, $replacement);
	}

	public function makeCylinder(Vector $pos, Pattern $block, float $radiusX, float $radiusZ, int $height, bool $filled) : int{
		$affected = 0;

		$radiusX += 0.5;
		$radiusZ += 0.5;

		if($height == 0){
			return 0;
		}else if($height < 0){
			$height = -$height;
			$pos = $pos->subtract(0, $height, 0);
		}

		if($pos->getBlockY() < 0){
			$pos = $pos->setY(0);
		}else if($pos->getBlockY() + $height - 1 > 255){
			$height = 255 - $pos->getBlockY() + 1;
		}

		$invRadiusX = 1 / $radiusX;
		$invRadiusZ = 1 / $radiusZ;

		$ceilRadiusX = (int) ceil($radiusX);
		$ceilRadiusZ = (int) ceil($radiusZ);

		$nextXn = 0;
		$forX = false;
		for($x = 0;$x <= $ceilRadiusX && !$forX;$x++){
			$xn = $nextXn;
			$nextXn = ($x + 1) * $invRadiusX;
			$nextZn = 0;
			$forZ = false;
			for($z = 0;$z <= $ceilRadiusZ && !$forZ;$z++){
				$zn = $nextZn;
				$nextZn = ($z + 1) * $invRadiusZ;

				$distanceSq = self::lengthSq($xn, $zn);
				if($distanceSq > 1){
					if($z == 0){
					$forX = true;
						break;
					}
					break;
				}

				if(!$filled){
					if(self::lengthSq($nextXn, $zn) <= 1 && self::lengthSq($xn, $nextZn) <= 1){
						continue;
					}
				}

				for($y = 0;$y < $height;$y++){
					if($this->setBlock($pos->add($x, $y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, $y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add($x, $y, -$z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, $y, -$z), $block)){
						++$affected;
					}
				}
			}
		}

		return $affected;
	}

	public function makeSphere(Vector $pos, Pattern $block, float $radiusX, float $radiusY, float $radiusZ, bool $filled) : int{
		$affected = 0;

		$radiusX += 0.5;
		$radiusY += 0.5;
		$radiusZ += 0.5;

		$invRadiusX = 1 / $radiusX;
		$invRadiusY = 1 / $radiusY;
		$invRadiusZ = 1 / $radiusZ;

		$ceilRadiusX = (int) ceil($radiusX);
		$ceilRadiusY = (int) ceil($radiusY);
		$ceilRadiusZ = (int) ceil($radiusZ);

		$nextXn = 0;
		$forX = false;
		for($x = 0;$x <= $ceilRadiusX && !$forX;$x++){
			$xn = $nextXn;
			$nextXn = ($x + 1) * $invRadiusX;
			$nextYn = 0;
			$forY = false;
			for ($y = 0;$y <= $ceilRadiusY && !$forY;$y++){
				$yn = $nextYn;
				$nextYn = ($y + 1) * $invRadiusY;
				$nextZn = 0;
				for($z = 0;$z <= $ceilRadiusZ;$z++){
					$zn = $nextZn;
					$nextZn = ($z + 1) * $invRadiusZ;

					$distanceSq = self::lengthSq($xn, $yn, $zn);
					if($distanceSq > 1){
						if($z == 0){
							if($y == 0){
								$forX = true;
								$forY = true;
								break;
							}
							$forY = true;
							break;
						}
						break;
					}

					if(!$filled){
						if(self::lengthSq($nextXn, $yn, $zn) <= 1 && self::lengthSq($xn, $nextYn, $zn) <= 1 && self::lengthSq($xn, $yn, $nextZn) <= 1){
							continue;
						}
					}

					if($this->setBlock($pos->add($x, $y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, $y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add($x, -$y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add($x, $y, -$z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, -$y, $z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add($x, -$y, -$z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, $y, -$z), $block)){
						++$affected;
					}
					if($this->setBlock($pos->add(-$x, -$y, -$z), $block)){
						++$affected;
					}
				}
			}
		}

		return $affected;
	}

	public function makePyramid(Vector $position, Pattern $block, int $size, bool $filled) : int{
		$affected = 0;

		$height = $size;

		for($y = 0;$y <= $height;$y++){
			$size--;
			for($x = 0;$x <= $size;$x++){
				for($z = 0;$z <= $size;$z++){

					if(($filled && $z <= $size && $x <= $size) || $z == $size || $x == $size){

						if($this->setBlock($position->add($x, $y, $z), $block)){
							++$affected;
						}
						if($this->setBlock($position->add(-$x, $y, $z), $block)){
							++$affected;
						}
						if($this->setBlock($position->add($x, $y, -$z), $block)){
							++$affected;
						}
						if($this->setBlock($position->add(-$x, $y, -$z), $block)){
							++$affected;
						}
					}
				}
			}
		}

		return $affected;
	}

	public function hollowOutRegion($region, $thickness, $pattern){
		$affected = 0;

		$outside = [];

		$min = $region->getMinimumPoint();
		$max = $region->getMaximumPoint();

		$minX = $min->getBlockX();
		$minY = $min->getBlockY();
		$minZ = $min->getBlockZ();
		$maxX = $max->getBlockX();
		$maxY = $max->getBlockY();
		$maxZ = $max->getBlockZ();

		for($x = $minX;$x <= $maxX;$x++){
			for($y = $minY;$y <= $maxY;$y++){
				$this->recurseHollow($region, new BlockVector($x, $y, $minZ), $outside);
				$this->recurseHollow($region, new BlockVector($x, $y, $maxZ), $outside);
			}
		}

		for($y = $minY;$y <= $maxY;$y++){
			for($z = $minZ;$z <= $maxZ;$z++){
				$this->recurseHollow($region, new BlockVector($minX, $y, $z), $outside);
				$this->recurseHollow($region, new BlockVector($maxX, $y, $z), $outside);
			}
		}

		for($z = $minZ;$z <= $maxZ;$z++){
			for($x = $minX;$x <= $maxX;$x++){
				$this->recurseHollow($region, new BlockVector($x, $minY, $z), $outside);
				$this->recurseHollow($region, new BlockVector($x, $maxY, $z), $outside);
			}
		}

		for($i = 1;$i < $thickness;$i++){
			$newOutside = [];
			foreach($region as $position){
				foreach(self::$recurseDirections as $recurseDirection){
					$neighbor = $position->add($recurseDirection)->toBlockVector();

					if(self::in_array($neighbor, $outside)){
						$newOutside[] = $position;
						break;
					}
				}
			}

			foreach($newOutside as $new){
				$outside[] = $new;
			}
		}

		foreach($region as $position){
			foreach(self::$recurseDirections as $recurseDirection){
				$neighbor = $position->add($recurseDirection)->toBlockVector();

				if(self::in_array($neighbor, $outside)) {
					break;
				}
			}

			if($this->setBlock($position, $pattern->next($position))){
				++$affected;
			}
		}

		return $affected;
	}

	public function drawLine($pattern, $pos1, $pos2, $radius, $filled){
		$vset = [];
		$notdrawn = true;

		$x1 = $pos1->getBlockX();
		$y1 = $pos1->getBlockY();
		$z1 = $pos1->getBlockZ();
		$x2 = $pos2->getBlockX();
		$y2 = $pos2->getBlockY();
		$z2 = $pos2->getBlockZ();
		$tipx = $x1;
		$tipy = $y1;
		$tipz = $z1;
		$dx = abs($x2 - $x1);
		$dy = abs($y2 - $y1);
		$dz = abs($z2 - $z1);

		if($dx + $dy + $dz == 0){
			$vset[] = new Vector($tipx, $tipy, $tipz);
			$notdrawn = false;
		}

		if(max(max($dx, $dy), $dz) == $dx && $notdrawn){
			for($domstep = 0;$domstep <= $dx;$domstep++){
				$tipx = $x1 + $domstep * ($x2 - $x1 > 0 ? 1 : -1);
				$tipy = (int) round($y1 + $domstep * ($dy) / ($dx) * ($y2 - $y1 > 0 ? 1 : -1));
				$tipz = (int) round($z1 + $domstep * ($dz) / ($dx) * ($z2 - $z1 > 0 ? 1 : -1));

				$vset[] = new Vector($tipx, $tipy, $tipz);
			}
			$notdrawn = false;
		}

		if(max(max($dx, $dy), $dz) == $dy && $notdrawn){
			for($domstep = 0;$domstep <= $dy;$domstep++){
				$tipy = $y1 + $domstep * ($y2 - $y1 > 0 ? 1 : -1);
				$tipx = (int) round($x1 + $domstep * ($dx) / ($dy) * ($x2 - $x1 > 0 ? 1 : -1));
				$tipz = (int) round($z1 + $domstep * ($dz) / ($dy) * ($z2 - $z1 > 0 ? 1 : -1));

				$vset[] = new Vector($tipx, $tipy, $tipz);
			}
			$notdrawn = false;
		}

		if(max(max($dx, $dy), $dz) == $dz && $notdrawn){
			for($domstep = 0;$domstep <= $dz;$domstep++){
				$tipz = z1 + domstep * (z2 - z1 > 0 ? 1 : -1);
				$tipy = (int) round($y1 + $domstep * ($dy) / ($dz) * ($y2 - $y1 > 0 ? 1 : -1));
				$tipx = (int) round($x1 + $domstep * ($dx) / ($dz) * ($x2 - $x1 > 0 ? 1 : -1));

				$vset[] = new Vector($tipx, $tipy, $tipz);
			}
			$notdrawn = false;
		}

		$vset = self::getBallooned($vset, $radius);
		if(!$filled){
			$vset = self::getHollowed($vset);
		}
		return $this->setBlocks($vset, $pattern);
	}

	public function drawSpline($pattern, $nodevectors, $tension, $bias, $continuity, $quality, $radius, $filled){
		$vset = [];
		$nodes = [];

		$interpol = new KochanekBartelsInterpolation();

		foreach($nodevectors as $nodevector){
			$n = new Node($nodevector);
			$n->setTension($tension);
			$n->setBias($bias);
			$n->setContinuity($continuity);
			$nodes[] = $n;
		}

		$interpol->setNodes($nodes);
		$splinelength = $interpol->arcLength(0, 1);
		for($loop = 0;$loop <= 1;$loop += 1 / $splinelength / $quality){
			$tipv = $interpol->getPosition($loop);
			$tipx = (int) round($tipv->getX());
			$tipy = (int) round($tipv->getY());
			$tipz = (int) round($tipv->getZ());

			$vset[] = new Vector($tipx, $tipy, $tipz);
		}

		$vset = self::getBallooned($vset, $radius);
		if(!$filled){
			$vset = self::getHollowed($vset);
		}
		return $this->setBlocks($vset, $pattern);
	}

	private static function hypot($pars){
		$sum = 0;
		foreach($pars as $d){
			$sum += pow($d, 2);
		}
		return sqrt($sum);
	}

	private static function getBallooned($vset, $radius) {
		$returnset = [];
		$ceilrad = (int) ceil($radius);

		foreach($vset as $v){
			$tipx = $v->getBlockX();
			$tipy = $v->getBlockY();
			$tipz = $v->getBlockZ();

			for($loopx = $tipx - $ceilrad;$loopx <= $tipx + $ceilrad;$loopx++){
				for($loopy = $tipy - $ceilrad;$loopy <= $tipy + $ceilrad;$loopy++){
					for($loopz = $tipz - $ceilrad;$loopz <= $tipz + $ceilrad;$loopz++){
						if(self::hypot([$loopx - $tipx, $loopy - $tipy, $loopz - $tipz]) <= $radius){
							$returnset[] = new Vector($loopx, $loopy, $loopz);
						}
					}
				}
			}
		}
		return $returnset;
	}

	private static function getHollowed($vset) {
		$returnset = [];
		foreach($vset as $v){
			$x = $v->getX();
			$y = $v->getY();
			$z = $v->getZ();
			if(self::in_array(new Vector(x + 1, y, z), $vset) &&
			self::in_array(new Vector(x - 1, y, z), $vset) &&
			self::in_array(new Vector(x, y + 1, z), $vset) &&
			self::in_array(new Vector(x, y - 1, z), $vset) &&
			self::in_array(new Vector(x, y, z + 1), $vset) &&
			self::in_array(new Vector(x, y, z - 1), $vset)){
				$returnset[] = $v;
			}
		}
		return $returnset;
	}

	private function recurseHollow($region, $origin, $outside){
		$queue = [];
		$queue[] = $origin;

		while(count($queue) > 0){
			foreach($queue as $k => $q){
				$current = $q;
				unset($queue[$k]);
				array_values($queue);
				break;
			}
			$block = $this->getBlock($current);
			if(!BlockType::canPassThrough($block->getId(), $block->getData())){
				continue;
			}

			if(self::in_array($current, $outline)){
				continue;
			}else{
				$outside[] = $current;
			}

			if(!self::in_array($current, $region)){
				continue;
			}

			foreach(self::$recurseDirections as $recurseDirection) {
				$queue[] = $current->add($recurseDirection)->toBlockVector();
			}
		}
	}

	private static function lengthSq($x, $y, $z = null) {
		if($z == null) return ($x * $x) + ($y * $y);
		return ($x * $x) + ($y * $y) + ($z * $z);
	}

	public static function in_array($vec, $vecs){
		foreach($vecs as $v){
			if($v->equals($vec)) return true;
		}

		return false;
	}
}