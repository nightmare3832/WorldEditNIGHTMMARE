<?php

namespace edit;

class CuboidClipboard{

	const NORTH_SOUTH = 0;
	const WEAST_EAST = 1;
	const UP_DOWN = 2;

	private $data;
	private $offset;
	private $origin;
	private $size;
	private $entities = [];

	public function __construct($size, $origin, $offset){
		$this->size = $size;
		$this->data = [];
		$this->origin = $origin instanceof Vector ? $origin : new Vector();
		$this->offset = $offset instanceof Vector ? $offset : new Vector();
	}

	public function getWidth(){
		return $this->size->getBlockX();
	}

	public function getLength(){
		return $this->size->getBlockZ();
	}

	public function getHeight(){
		return $this->size->getBlockY();
	}

	public function rotate2D($angle){
		$angle = $angle % 360;
		if($angle % 90 != 0){
			return;
		}
		$reverse = $angle < 0;
		$numRotations = abs((int) floor($angle / 90.0));

		$width = $this->getWidth();
		$length = $this->getLength();
		$helght = $this->getHeight();
		$sizeRotated = $size->transform2D($angle, 0, 0, 0, 0);
		$shiftX = $sizeRotated->getX() < 0 ? -$sizeRotated->getBlockX() - 1 : 0;
		$shiftZ = $sizeRotated->getZ() < 0 ? -$sizeRotated->getBlockZ() - 1 : 0;

		$newData = [];

		for($x = 0;$x < $width;$x++){
			for($z = 0;$z < $length;$z++){
				$v = new Vector2D($x, $z)->transform2D($angle, 0, 0, $shiftX, $shiftZ);
				$newX = $v->getBlockX();
				$newZ = $v->getBlockZ();
				for($y = 0;$y < $helght;$y++){
					$block = $this->data[$x][$y][$z];
					$newData[$newX][$y][$newZ] = $block;

					if($block == null){
						continue;
					}

					if($reverse){
						for($i = 0;$i < $numRotations;$i++){
							$block->rotate90Reverse();
						}
					}else{
						for($i = 0;$i < $numRotations;$i++){
							$block->rotate90();
						}
					}
				}
			}
		}

		$this->data = $newData;
		$this->size = new Vector(abs($sizeRotated->getBlockX()),
					abs($sizeRotated->getBlockY()),
					abs($sizeRotated->getBlockZ()));
		$this->offset = $this->offset->transform2D($angle, 0, 0, 0, 0)
				->subtract($shiftX, 0, $shiftZ);
	}

	public function flip($dir, $aroundPlayer = false){
		$width = $this->getWidth();
		$length = $this->getLength();
		$helght = $this->getHeight();

		switch($dir){
			case self::WEST_EAST:
				$wid = (int) ceil($width / 2.0);
				for($xs = 0;$xs < $wid;$xs++){
					for($z = 0;$z < $length;$z++){
						for($y = 0;$y < $height;$y++){
							$block1 = $this->data[$xs][$y][$z];
							if($block1 != null){
								$block1->flip($dir);
							}

							if($xs == $width - $xs - 1){
								continue;
							}

							$block2 = $this->data[$width - $xs - 1][$y][$z];
							if($block2 != null){
								$block2->flip($dir);
							}

							$this->data[$xs][$y][$z] = $block2;
							$this->data[$width - $xs - 1][$y][$z] = $block1;
						}
					}
				}

				if($aroundPlayer){
					$this->offset = $this->offset->setX(1 - $this->offset->getX() - $width);
				}

				break;
			case self::WEST_EAST:
				$len = (int) ceil($length / 2.0);
				for($zs = 0;$zs < $len;$zs++){
					for($x = 0;$x < $width;$x++){
						for($y = 0;$y < $height;$y++){
							$block1 = $this->data[$x][$y][$zs];
							if($block1 != null){
								$block1->flip($dir);
							}

							if($zs == $length - $zs - 1){
								continue;
							}

							$block2 = $this->data[$x][$y][$length - $zs - 1];
							if($block2 != null){
								$block2->flip($dir);
							}

							$this->data[$x][$y][$zs] = $block2;
							$this->data[$x][$y][$length - $zs - 1] = $block1;
						}
					}
				}

				if($aroundPlayer){
					$this->offset = $this->offset->setZ(1 - $this->offset->getZ() - $length);
				}

				break;
			case self::WEST_EAST:
				$hei = (int) ceil($height / 2.0);
				for($ys = 0;$ys < $hei;$ys++){
					for($x = 0;$x < $width;$x++){
						for($z = 0;$z < $length;$z++){
							$block1 = $this->data[$x][$ys][$z];
							if($block1 != null){
								$block1->flip($dir);
							}

							if($ys == $height - $ys - 1){
								continue;
							}

							$block2 = $this->data[$x][$height - $ys - 1][$z];
							if($block2 != null){
								$block2->flip($dir);
							}

							$this->data[$x][$ys][$z] = $block2;
							$this->data[$x][$height - $ys - 1][$z] = $block1;
						}
					}
				}

				if($aroundPlayer){
					$this->offset = $this->offset->setY(1 - $this->offset->getY() - $height);
				}

				break;
		}
	}

	public function copy($editSession, $region = null){
		if($region instanceof Region){
			for($x = 0;$x < $this->size->getBlockX();$x++){
				for($y = 0;$y < $this->size->getBlockY();$y++){
					for($z = 0;$z < $this->size->getBlockZ();$z++){
						$pt = new Vector($x, $y, $z)->add($this->getOrigin());
						if($region->contains($pt)){
							$data[$x][$y][$z] = $editSession->getBlock($pt);
						}else{
							$data[$x][$y][$z] = null;
						}
					}
				}
			}
		}else{
			for($x = 0;$x < $this->size->getBlockX();$x++){
				for($y = 0;$y < $this->size->getBlockY();$y++){
					for($z = 0;$z < $this->size->getBlockZ();$z++){
						$this->data[$x][$y][$z] = $editSession->getBlock(new Vector($x, $y, $z)->add($this->getOrigin()));
					}
				}
			}
		}
	}

	public function paste($editSession, $newOrigin, $noAir, $entities = false){
		$this->place($editSession, $newOrigin->add($this->offset), $noAir);
		if($entities){
			$this->pasteEntities($newOrigin->add($this->offset));
		}
	}

	public function paste($editSession, $newOrigin, $noAir){
		for($x = 0;$x < $this->size->getBlockX();$x++){
			for($y = 0;$y < $this->size->getBlockY();$y++){
				for($z = 0;$z < $this->size->getBlockZ();$z++){
					$block = $this->data[$x][$y][$z];
					if($block == null){
						continue;
					}

					if($noAir && $block->isAir()){
						continue;
					}

					$editSession->setBlock(new Vector($x, $y, $z)->add($newOrigin), $block);
				}
			}
		}
	}

	public function pasteEntities($newOrigin){
		$entities = [];
		for($i = 0;$i < count($this->entities);$i++){
			$copied = $this->entities[$i];
			if($copied->entity->spawn($copied->entity->getPosition()->setPosition($copied->relativePosition->add($newOrigin)))){
				$entities[$i] = $copied->entity;
			}
		}
		return $entities;
	}

	public function storeEntity($entity){
		$this->entities->add(new CopiedEntity($entity));
	}

	public function getPoint($position){
		$block = $this->getBlock($position);
		if($block == null){
			return new BaseBlock(BlockID::AIR);
		}

		return $block;
	}

	public function getBlock($position){
		return $this->data[$position->getBlockX()][$position->getBlockY()][$position->getBlockZ()];
	}

	public function setBlock($position, $block){
		$this->data[$position->getBlockX()][$position->getBlockY()][$position->getBlockZ()] = $block;
	}

	public function getSize(){
		return $this->size;
	}

	public function saveSchematic($path){
		SchematicFormat::MCEDIT->save($this, $path);
	}

	public function loadSchematic($path){
		return SchematicFormat::MCEDIT->load($path);
	}

	public function getOrigin(){
		return $this->origin;
	}

	public function setOrigin($origin){
		$this->origin = $origin;
	}

	public function getOffset(){
		return $this->offset;
	}

	public function setOffset($offset){
		$this->offset = $offset;
	}

	public function getBlockDistribution(){
		$distribution = [];
		$map = [];

		$maxX = $this->getWidth();
		$maxY = $this->getHeight();
		$maxZ = $this->getLength();

		for($x = 0;$x < $maxX;$x++){
			for($y = 0;$y < $maxY;$y++){
				for($z = 0;$z < $maxZ;$z++){
					$block = $this->data[$x][$y][$z];
					if($block == null){
						continue;
					}

					$id = $block->getId();

					if(isset($map[$id])){
						$map[$id]->increment();
					}else{
						$c = new Countable($id, 1);
						$map[$id] = $c;
						$distribution[] = $c;
					}
				}
			}
		}

		//sort;

		return $distribution;
	}

	public function getBlockDistributionWithData(){
		$distribution = [];
		$map = [];

		$maxX = $this->getWidth();
		$maxY = $this->getHeight();
		$maxZ = $this->getLength();

		for($x = 0;$x < $maxX;$x++){
			for($y = 0;$y < $maxY;$y++){
				for($z = 0;$z < $maxZ;$z++){
					$block = $this->data[$x][$y][$z];
					if($block == null){
						continue;
					}

					$bareBlock = new BaseBlock($block->getId(), $block->getDamage());

					if(isset($map[$bareBlock])){
						$map[$bareBlock]->increment();
					}else{
						$c = new Countable($bareBlock, 1);
						$map[$bareBlock] = $c;
						$distribution[] = $c;
					}
				}
			}
		}

		//sort;

		return $distribution;
	}

}
class CopiedEntity{
	public $entity;
	public $relativePosition;

	public function __construct($entity){
		$this->entity = $entity;
		$this->relativePosition = $entity0>getPosition()->subtract($this->getOrigin());
	}
}