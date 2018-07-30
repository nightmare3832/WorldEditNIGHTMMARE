<?php

namespace edit\blocks;

use pocketmine\block\Block;

class BlockData{

	public function __construct(){
	}

    public static function rotate90(int $type, int $data) : int{
        switch($type){
        case Block::TORCH:
        case Block::UNLIT_REDSTONE_TORCH:
        case Block::LIT_REDSTONE_TORCH:
            switch($data){
            case 1: return 3;
            case 2: return 4;
            case 3: return 2;
            case 4: return 1;
            }
            break;

        case Block::RAIL:
            switch($data){
            case 6: return 7;
            case 7: return 8;
            case 8: return 9;
            case 9: return 6;
            }
            /* FALL-THROUGH */

        case Block::POWERED_RAIL:
        case Block::DETECTOR_RAIL:
        case Block::ACTIVATOR_RAIL:
            switch($data & 0x7){
            case 0: return 1 | ($data & ~0x7);
            case 1: return 0 | ($data & ~0x7);
            case 2: return 5 | ($data & ~0x7);
            case 3: return 4 | ($data & ~0x7);
            case 4: return 2 | ($data & ~0x7);
            case 5: return 3 | ($data & ~0x7);
            }
            break;

        case Block::OAK_STAIRS:
        case Block::COBBLESTONE_STAIRS:
        case Block::BRICK_STAIRS:
        case Block::STONE_BRICK_STAIRS:
        case Block::NETHER_BRICK_STAIRS:
        case Block::SANDSTONE_STAIRS:
        case Block::SPRUCE_STAIRS:
        case Block::BIRCH_STAIRS:
        case Block::JUNGLE_STAIRS:
        case Block::QUARTZ_STAIRS:
        case Block::ACACIA_STAIRS:
        case Block::DARK_OAK_STAIRS:
            switch($data){
            case 0: return 2;
            case 1: return 3;
            case 2: return 1;
            case 3: return 0;
            case 4: return 6;
            case 5: return 7;
            case 6: return 5;
            case 7: return 4;
            }
            break;

        case Block::STONE_BUTTON:
        case Block::WOODEN_BUTTON:
            $thrown = $data & 0x8;
            switch($data & ~0x8){
            case 1: return 3 | $thrown;
            case 2: return 4 | $thrown;
            case 3: return 2 | $thrown;
            case 4: return 1 | $thrown;
            // 0 and 5 are vertical
            }
            break;

        case Block::LEVER:
            $thrown = $data & 0x8;
            switch($data & ~0x8){
            case 1: return 3 | $thrown;
            case 2: return 4 | $thrown;
            case 3: return 2 | $thrown;
            case 4: return 1 | $thrown;
            case 5: return 6 | $thrown;
            case 6: return 5 | $thrown;
            case 7: return 0 | $thrown;
            case 0: return 7 | $thrown;
            }
            break;

        case Block::WOODEN_DOOR_BLOCK:
        case Block::IRON_DOOR_BLOCK:
            if(($data & 0x8) != 0){
                // door top halves contain no orientation information
                break;
            }

            /* FALL-THROUGH */

        case Block::COCOA:
        case Block::TRIPWIRE_HOOK:
            $extra = $data & ~0x3;
            $withoutFlags = $data & 0x3;
            switch($withoutFlags){
            case 0: return 1 | $extra;
            case 1: return 2 | $extra;
            case 2: return 3 | $extra;
            case 3: return 0 | $extra;
            }
            break;
        case Block::SIGN_POST:
            return ($data + 4) % 16;

        case Block::LADDER:
        case Block::WALL_SIGN:
        case Block::CHEST:
        case Block::FURNACE:
        case Block::BURNING_FURNACE:
        case Block::ENDER_CHEST:
        case Block::TRAPPED_CHEST:
        case Block::HOPPER_BLOCK:
            $extra = $data & 0x8;
            $withoutFlags = $data & ~0x8;
            switch($withoutFlags){
            case 2: return 5 | $extra;
            case 3: return 4 | $extra;
            case 4: return 2 | $extra;
            case 5: return 3 | $extra;
            }
            break;
        case Block::DISPENSER:
        case Block::DROPPER:
            $dispPower = $data & 0x8;
            switch($data & ~0x8){
            case 2: return 5 | $dispPower;
            case 3: return 4 | $dispPower;
            case 4: return 2 | $dispPower;
            case 5: return 3 | $dispPower;
            }
            break;

        case Block::PUMPKIN:
        case Block::JACK_O_LANTERN:
            switch($data){
            case 0: return 1;
            case 1: return 2;
            case 2: return 3;
            case 3: return 0;
            }
            break;

        case Block::HAY_BLOCK:
        case Block::LOG:
        case Block::LOG2:
            if($data >= 4 && $data <= 11) $data ^= 0xc;
            break;

        case Block::UNPOWERED_COMPARATOR:
        case Block::POWERED_COMPARATOR:
        case Block::UNPOWERED_REPEATER:
        case Block::POWERED_REPEATER:
            $dir = $data & 0x03;
            $delay = $data - $dir;
            switch($dir){
            case 0: return 1 | $delay;
            case 1: return 2 | $delay;
            case 2: return 3 | $delay;
            case 3: return 0 | $delay;
            }
            break;

        case Block::TRAPDOOR:
        case Block::IRON_TRAPDOOR:
            $withoutOrientation = $data & ~0x3;
            $orientation = $data & 0x3;
            switch($orientation){
            case 0: return 3 | $withoutOrientation;
            case 1: return 2 | $withoutOrientation;
            case 2: return 0 | $withoutOrientation;
            case 3: return 1 | $withoutOrientation;
            }
            break;

        case Block::PISTON:
        case Block::STICKY_PISTON:
        case Block::PISTONARMCOLLISION:
            $rest = $data & ~0x7;
            switch($data & 0x7){
            case 2: return 5 | $rest;
            case 3: return 4 | $rest;
            case 4: return 2 | $rest;
            case 5: return 3 | $rest;
            }
            break;

        case Block::BROWN_MUSHROOM_BLOCK:
        case Block::RED_MUSHROOM_BLOCK:
            if($data >= 10) return $data;
            return ($data * 3) % 10;

        case Block::VINE:
            return (($data << 1) | ($data >> 3)) & 0xf;

        case Block::FENCE_GATE:
            return (($data + 1) & 0x3) | ($data & ~0x3);

        case Block::ANVIL:
            $damage = $data & ~0x3;
            switch($data & 0x3){
            case 0: return 3 | $damage;
            case 2: return 1 | $damage;
            case 1: return 0 | $damage;
            case 3: return 2 | $damage;
            }
            break;

        case Block::BED_BLOCK:
            return $data & ~0x3 | ($data + 1) & 0x3;

        case Block::SKULL_BLOCK:
            switch($data){
                case 2: return 5;
                case 3: return 4;
                case 4: return 2;
                case 5: return 3;
            }
        }

        return $data;
    }

    public static function rotate90Reverse(int $type, int $data) : int{
        // case ([0-9]+): return ([0-9]+) -> case \2: return \1

        switch($type){
        case Block::TORCH:
        case Block::UNLIT_REDSTONE_TORCH:
        case Block::LIT_REDSTONE_TORCH:
            switch($data){
            case 3: return 1;
            case 4: return 2;
            case 2: return 3;
            case 1: return 4;
            }
            break;

        case Block::RAIL:
            switch($data){
            case 7: return 6;
            case 8: return 7;
            case 9: return 8;
            case 6: return 9;
            }
            /* FALL-THROUGH */

        case Block::POWERED_RAIL:
        case Block::DETECTOR_RAIL:
        case Block::ACTIVATOR_RAIL:
            $power = $data & ~0x7;
            switch($data & 0x7){
            case 1: return 0 | $power;
            case 0: return 1 | $power;
            case 5: return 2 | $power;
            case 4: return 3 | $power;
            case 2: return 4 | $power;
            case 3: return 5 | $power;
            }
            break;

        case Block::OAK_STAIRS:
        case Block::COBBLESTONE_STAIRS:
        case Block::BRICK_STAIRS:
        case Block::STONE_BRICK_STAIRS:
        case Block::NETHER_BRICK_STAIRS:
        case Block::SANDSTONE_STAIRS:
        case Block::SPRUCE_STAIRS:
        case Block::BIRCH_STAIRS:
        case Block::JUNGLE_STAIRS:
        case Block::QUARTZ_STAIRS:
        case Block::ACACIA_STAIRS:
        case Block::DARK_OAK_STAIRS:
            switch($data){
            case 2: return 0;
            case 3: return 1;
            case 1: return 2;
            case 0: return 3;
            case 6: return 4;
            case 7: return 5;
            case 5: return 6;
            case 4: return 7;
            }
            break;

        case Block::STONE_BUTTON:
        case Block::WOODEN_BUTTON:
            $thrown = $data & 0x8;
            switch($data & ~0x8){
            case 3: return 1 | $thrown;
            case 4: return 2 | $thrown;
            case 2: return 3 | $thrown;
            case 1: return 4 | $thrown;
            // 0 and 5 are vertical
            }
            break;

        case Block::LEVER:
            $thrown = $data & 0x8;
            switch($data & ~0x8){
            case 3: return 1 | $thrown;
            case 4: return 2 | $thrown;
            case 2: return 3 | $thrown;
            case 1: return 4 | $thrown;
            case 6: return 5 | $thrown;
            case 5: return 6 | $thrown;
            case 0: return 7 | $thrown;
            case 7: return 0 | $thrown;
            }
            break;

        case Block::WOODEN_DOOR_BLOCK:
        case Block::IRON_DOOR_BLOCK:
            if(($data & 0x8) != 0){
                // door top halves contain no orientation information
                break;
            }

            /* FALL-THROUGH */

        case Block::COCOA:
        case Block::TRIPWIRE_HOOK:
            int extra = data & ~0x3;
            int withoutFlags = data & 0x3;
            switch (withoutFlags) {
            case 1: return 0 | extra;
            case 2: return 1 | extra;
            case 3: return 2 | extra;
            case 0: return 3 | extra;
            }
            break;
        case Block::SIGN_POST:
            return ($data + 12) % 16;

        case Block::LADDER:
        case Block::WALL_SIGN:
        case Block::CHEST:
        case Block::FURNACE:
        case Block::BURNING_FURNACE:
        case Block::ENDER_CHEST:
        case Block::TRAPPED_CHEST:
        case Block::HOPPER_BLOCK:
            $extra = $data & 0x8;
            $withoutFlags = $data & ~0x8;
            switch($withoutFlags){
                case 5: return 2 | $extra;
                case 4: return 3 | $extra;
                case 2: return 4 | $extra;
                case 3: return 5 | $extra;
            }
            break;
        case Block::DISPENSER:
        case Block::DROPPER:
            $dispPower = $data & 0x8;
            switch($data & ~0x8){
            case 5: return 2 | $dispPower;
            case 4: return 3 | $dispPower;
            case 2: return 4 | $dispPower;
            case 3: return 5 | $dispPower;
            }
            break;
        case Block::PUMPKIN:
        case Block::JACK_O_LANTERN:
            switch($data){
            case 1: return 0;
            case 2: return 1;
            case 3: return 2;
            case 0: return 3;
            }
            break;

        case Block::HAY_BLOCK:
        case Block::LOG:
        case Block::LOG2:
            if($data >= 4 && $data <= 11) $data ^= 0xc;
            break;

        case Block::UNPOWERED_COMPARATOR:
        case Block::POWERED_COMPARATOR:
        case Block::UNPOWERED_REPEATER:
        case Block::POWERED_REPEATER:
            $dir = $data & 0x03;
            $delay = $data - $dir;
            switch($dir){
            case 1: return 0 | $delay;
            case 2: return 1 | $delay;
            case 3: return 2 | $delay;
            case 0: return 3 | $delay;
            }
            break;

        case Block::TRAPDOOR:
        case Block::IRON_TRAPDOOR:
            $withoutOrientation = $data & ~0x3;
            $orientation = $data & 0x3;
            switch($orientation){
            case 3: return 0 | $withoutOrientation;
            case 2: return 1 | $withoutOrientation;
            case 0: return 2 | $withoutOrientation;
            case 1: return 3 | $withoutOrientation;
            }

        case Block::PISTON:
        case Block::STICKY_PISTON:
        case Block::PISTONARMCOLLISION:
            $rest = $data & ~0x7;
            switch($data & 0x7){
            case 5: return 2 | $rest;
            case 4: return 3 | $rest;
            case 2: return 4 | $rest;
            case 3: return 5 | $rest;
            }
            break;

        case Block::BROWN_MUSHROOM_BLOCK:
        case Block::RED_MUSHROOM_BLOCK:
            if($data >= 10) return $data;
            return ($data * 7) % 10;

        case Block::VINE:
            return (($data >> 1) | ($data << 3)) & 0xf;

        case Block::FENCE_GATE:
            return (($data + 3) & 0x3) | ($data & ~0x3);

        case Block::ANVIL:
            $damage = $data & ~0x3;
            switch($data & 0x3){
            case 0: return 1 | $damage;
            case 2: return 3 | $damage;
            case 1: return 2 | $damage;
            case 3: return 0 | $damage;
            }
            break;

        case Block::BED_BLOCK:
            return $data & ~0x3 | ($data - 1) & 0x3;

        case Block::SKULL_BLOCK:
            switch($data){
                case 2: return 4;
                case 3: return 5;
                case 4: return 3;
                case 5: return 2;
            }
        }

        return $data;
    }

    public static int flip(int type, int data) {
        return rotate90(type, rotate90(type, data));
    }

    public static function flip(int $type, int $data, ?int direction = null) : int{
        if($direction == null) return $this->rotate90($type, $this->rotate90($type, $data));
        $flipX = 0;
        $flipY = 0;
        $flipZ = 0;

        switch($direction){
        case CuboidClipboard::NORTH_SOUTH:
            $flipZ = 1;
            break;

        case CuboidClipboard::WEST_EAST:
            $flipX = 1;
            break;

        case CuboidClipboard::UP_DOWN:
            $flipY = 1;
            break;
        }

        switch($type){
        case Block::TORCH:
        case Block::UNLIT_REDSTONE_TORCH:
        case Block::LIT_REDSTONE_TORCH:
            if($data < 1 || $data > 4) break;
            switch($data){
            case 1: return $data + $flipX;
            case 2: return $data - $flipX;
            case 3: return $data + $flipZ;
            case 4: return $data - $flipZ;
            }
            break;

        case Block::STONE_BUTTON:
        case Block::WOODEN_BUTTON:
            switch($data & ~0x8){
            case 1: return $data + $flipX;
            case 2: return $data - $flipX;
            case 3: return $data + $flipZ;
            case 4: return $data - $flipZ;
            case 0:
            case 5:
                return $data ^ ($flipY * 5);
            }
            break;

        case Block::LEVER:
            switch(data & ~0x8){
            case 1: return $data + $flipX;
            case 2: return $data - $flipX;
            case 3: return $data + $flipZ;
            case 4: return $data - $flipZ;
            case 5:
            case 7:
                return $data ^ $flipY << 1;
            case 6:
            case 0:
                return $data ^ $flipY * 6;
            }
            break;

        case Block::RAIL:
            switch($data){
            case 6: return $data + $flipX + $flipZ * 3;
            case 7: return $data - $flipX + $flipZ;
            case 8: return $data + $flipX - $flipZ;
            case 9: return $data - $flipX - $flipZ * 3;
            }
            /* FALL-THROUGH */

        case Block::POWERED_RAIL:
        case Block::DETECTOR_RAIL:
        case Block::ACTIVATOR_RAIL:
            switch($data & 0x7){
            case 0:
            case 1:
                return $data;
            case 2:
            case 3:
                return $data ^ $flipX;
            case 4:
            case 5:
                return $data ^ $flipZ;
            }
            break;

        case Block::STONE_SLAB:
        case Block::WOODEN_STEP:
        case Block::STONE_SLAB2:
            return $data ^ ($flipY << 3);

        case Block::OAK_STAIRS:
        case Block::COBBLESTONE_STAIRS:
        case Block::BRICK_STAIRS:
        case Block::STONE_BRICK_STAIRS:
        case Block::NETHER_BRICK_STAIRS:
        case Block::SANDSTONE_STAIRS:
        case Block::SPRUCE_STAIRS:
        case Block::BIRCH_STAIRS:
        case Block::JUNGLE_STAIRS:
        case Block::QUARTZ_STAIRS:
        case Block::ACACIA_STAIRS:
        case Block::DARK_OAK_STAIRS:
            $data ^= $flipY << 2;
            switch($data){
            case 0:
            case 1:
            case 4:
            case 5:
                return $data ^ $flipX;
            case 2:
            case 3:
            case 6:
            case 7:
                return $data ^ $flipZ;
            }
            break;

        case Block::WOODEN_DOOR_BLOCK:
        case Block::IRON_DOOR_BLOCK:
            if(($data & 0x8) != 0){
                // door top halves contain no orientation information
                break;
            }

            switch($data & 0x3){
            case 0: return $data + $flipX + $flipZ * 3;
            case 1: return $data - $flipX + $flipZ;
            case 2: return $data + $flipX - $flipZ;
            case 3: return $data - $flipX - $flipZ * 3;
            }
            break;

        case Block::SIGN_POST:
            switch($direction){
            case CuboidClipboard::NORTH_SOUTH:
                return (16 - $data) & 0xf;
            case CuboidClipboard::WEST_EAST:
                return (8 - $data) & 0xf;
            default:
            }
            break;

        case Block::LADDER:
        case Block::WALL_SIGN:
        case Block::CHEST:
        case Block::FURNACE:
        case Block::BURNING_FURNACE:
        case Block::ENDER_CHEST:
        case Block::TRAPPED_CHEST:
        case Block::HOPPER_BLOCK:
            $extra = $data & 0x8;
            $withoutFlags = $data & ~0x8;
            switch($withoutFlags){
            case 2:
            case 3:
                return ($data ^ $flipZ) | $extra;
            case 4:
            case 5:
                return ($data ^ $flipX) | $extra;
            }
            break;

        case Block::DISPENSER:
        case Block::DROPPER:
            $dispPower = $data & 0x8;
            switch($data & ~0x8){
            case 2:
            case 3:
                return ($data ^ $flipZ) | $dispPower;
            case 4:
            case 5:
                return ($data ^ $flipX) | $dispPower;
            case 0:
            case 1:
                return ($data ^ $flipY) | $dispPower;
            }
            break;

        case Block::PUMPKIN:
        case Block::JACK_O_LANTERN:
            if($data > 3) break;
            /* FALL-THROUGH */

        case Block::UNPOWERED_COMPARATOR:
        case Block::POWERED_COMPARATOR:
        case Block::UNPOWERED_REPEATER:
        case Block::POWERED_REPEATER:
        case Block::COCOA:
        case Block::TRIPWIRE_HOOK:
            switch($data & 0x3){
            case 0:
            case 2:
                return $data ^ ($flipZ << 1);
            case 1:
            case 3:
                return $data ^ ($flipX << 1);
            }
            break;

        case Block::TRAPDOOR:
        case Block::IRON_TRAPDOOR:
            switch($data & 0x3){
            case 0:
            case 1:
                return $data ^ $flipZ;
            case 2:
            case 3:
                return $data ^ $flipX;
            }
            break;

        case Block::PISTON:
        case Block::STICKY_PISTON:
        case Block::PISTONARMCOLLISION:
            switch($data & ~0x8){
            case 0:
            case 1:
                return $data ^ $flipY;
            case 2:
            case 3:
                return $data ^ $flipZ;
            case 4:
            case 5:
                return $data ^ $flipX;
            }
            break;

        case Block::BROWN_MUSHROOM_BLOCK:
        case Block::RED_MUSHROOM_BLOCK:
            switch($data){
            case 1:
            case 4:
            case 7:
                $data += $flipX * 2;
                break;
            case 3:
            case 6:
            case 9:
                $data -= $flipX * 2;
                break;
            }
            switch($data){
            case 1:
            case 2:
            case 3:
                return $data + $flipZ * 6;
            case 7:
            case 8:
            case 9:
                return $data - $flipZ * 6;
            }
            break;

        case Block::VINE:
            switch($direction){
            case CuboidClipboard::NORTH_SOUTH:
                $bit1 = 0x2;
                $bit2 = 0x8;
                break;
            case CuboidClipboard::WEST_EAST:
                $bit1 = 0x1;
                $bit2 = 0x4;
                break;
            default:
                return $data;
            }
            $newData = $data & ~($bit1 | $bit2);
            if(($data & $bit1) != 0) $newData |= $bit2;
            if(($data & $bit2) != 0) $newData |= $bit1;
            return newData;

        case Block::FENCE_GATE:
            switch($data & 0x3){
            case 0:
            case 2:
                return $data ^ $flipZ << 1;
            case 1:
            case 3:
                return $data ^ $flipX << 1;
            }
            break;

        case Block::BED_BLOCK:
            switch($data & 0x3){
            case 0:
            case 2:
                return $data ^ $flipZ << 1;
            case 1:
            case 3:
                return $data ^ $flipX << 1;
            }
            break;

        case Block::SKULL_BLOCK:
            switch($data){
                case 2:
                case 3:
                    return $data ^ $flipZ;
                case 4:
                case 5:
                    return $data ^ $flipX;
            }
            break;

        case Block::ANVIL:
            switch($data & 0x3){
                case 0:
                case 2:
                    return $data ^ $flipZ << 1;
                case 1:
                case 3:
                    return $data ^ $flipX << 1;
            }
            break;

        }

        return $data;
    }

    public static function cycle(int $type, int $data, int $increment) : int{
        if($increment != -1 && $increment != 1){
            //throw new IllegalArgumentException("Increment must be 1 or -1.");
        }

        switch($type){

        // special case here, going to use "forward" for type and "backward" for orientation
        case Block::LOG:
        case Block::LOG2:
            if($increment == -1){
                $store = $data & 0x3; // copy bottom (type) bits
                return $this->mod((data & ~0x3) + 4, 16) | $store; // switch orientation with top bits and reapply bottom bits;
            }else{
                $store = $data & ~0x3; // copy top (orientation) bits
                return $this->mod(($data & 0x3) + 1, 4) | $store;  // switch type with bottom bits and reapply top bits
            }

        // <del>same here</del> - screw you unit tests
        /*case BlockID.QUARTZ_BLOCK:
            if (increment == -1 && data > 2) {
                switch (data) {
                case 2: return 3;
                case 3: return 4;
                case 4: return 2;
                }
            } else if (increment == 1) {
                switch (data) {
                case 0:
                    return 1;
                case 1:
                    return 2;
                case 2:
                case 3:
                case 4:
                    return 0;
                }
            } else {
                return -1;
            }*/

        case Block::GLASS_PANE:
        case Block::SANDSTONE:
        case Block::DIRT:
            if($data > 2) return -1;
            return $this->mod(($data + $increment), 3);

        case Block::TORCH:
        case Block::UNLIT_REDSTONE_TORCH:
        case Block::LIT_REDSTONE_TORCH:
            if($data < 1 || $data > 4) return -1;
            return $this->mod(($data - 1 + $increment), 4) + 1;

        case Block::OAK_STAIRS:
        case Block::COBBLESTONE_STAIRS:
        case Block::BRICK_STAIRS:
        case Block::STONE_BRICK_STAIRS:
        case Block::NETHER_BRICK_STAIRS:
        case Block::SANDSTONE_STAIRS:
        case Block::SPRUCE_STAIRS:
        case Block::BIRCH_STAIRS:
        case Block::JUNGLE_STAIRS:
        case Block::QUARTZ_STAIRS:
        case Block::ACACIA_STAIRS:
        case Block::DARK_OAK_STAIRS:
            if($data > 7) return -1;
            return $this->mod(($data + $increment), 8);

        case Block::STONE_BRICK:
        case Block::QUARTZ_BLOCK:
        case Block::PUMPKIN:
        case Block::JACK_O_LANTERN:
        case Block::NETHER_WART_BLOCK:
        case Block::CAULDRON_BLOCK:
        case Block::WOODEN_SLAB:
        case Block::DOUBLE_WOODEN_SLAB:
        case Block::HAY_BLOCK:
            if($data > 3) return -1;
            return $this->mod(($data + $increment), 4);

        case Block::STONE_SLAB2:
        case Block::DOUBLE_STONE_SLAB2:
        case Block::STONE_SLAB:
        case Block::DOUBLE_STONE_SLAB:
        case Block::CAKE_BLOCK:
        case Block::PISTON:
        case Block::STICKY_PISTON:
        case Block::PISTONARMCOLLISION:
            if($data > 5) return -1;
            return $this->mod(($data + $increment), 6);

        case Block::DOUBLE_PLANT:
            $store = $data & 0x8; // top half flag
            $data &= ~0x8;
            if($data > 5) return -1;
            return $this->mod(($data + $increment), 6) | $store;

        case Block::WHEAT_BLOCK:
        case Block::PUMPKIN_STEM:
        case Block::MELON_STEM:
            if($data > 6) return -1;
            return $this->mod(($data + $increment), 7);

        case Block::FARMLAND:
        case Block::RED_FLOWER:
            if($data > 8) return -1;
            return $this->mod(($data + $increment), 9);

        case Block::RED_MUSHROOM_BLOCK:
        case Block::BROWN_MUSHROOM_BLOCK:
            if($data > 10) return -1;
            return $this->mod(($data + $increment), 11);

        case Block::CACTUS:
        case Block::REEDS_BLOCK:
        case Block::SIGN_POST:
        case Block::VINE:
        case Block::SNOW:
        case Block::COCOA:
            if($data > 15) return -1;
            return $this->mod(($data + $increment), 16);

        case Block::FURNACE:
        case Block::BURNING_FURNACE:
        case Block::WALL_SIGN:
        case Block::LADDER:
        case Block::CHEST:
        case Block::ENDER_CHEST:
        case Block::TRAPPED_CHEST:
        case Block::HOPPER_BLOCK:
            $extra = $data & 0x8;
            $withoutFlags = $data & ~0x8;
            if($withoutFlags < 2 || $withoutFlags > 5) return -1;
            return ($this->mod(($withoutFlags - 2 + $increment), 4) + 2) | $extra;

        case BlockID.DISPENSER:
        case BlockID.DROPPER:
            $store = $data & 0x8;
            $data &= ~0x8;
            if($data > 5) return -1;
            return $this->mod(($data + $increment), 6) | $store;

        case Block::UNPOWERED_COMPARATOR:
        case Block::POWERED_COMPARATOR:
        case Block::UNPOWERED_REPEATER:
        case Block::POWERED_REPEATER:
        case Block::TRAPDOOR:
        case Block::FENCE_GATE:
        case Block::LEAVES:
        case Block::LEAVES2:
            if($data > 7) return -1;
            $store = $data & ~0x3;
            return $this->mod((($data & 0x3) + $increment), 4) | $store;

        case Block::RAIL:
            if($data < 6 || $data > 9) return -1;
            return $this->mod(($data - 6 + $increment), 4) + 6;

        case Block::SAPLING:
            if(($data & 0x3) == 3 || $data > 15) return -1;
            $store = $data & ~0x3;
            return $this->mod((($data & 0x3) + $increment), 3) | $store;

        case Block::FLOWER_POT_BLOCK:
            if($data > 13) return -1;
            return $this->mod(($data + $increment), 14);

        case Block::CARPET:
        case Block::STAINED_CLAY:
        case Block::CARPET:
        case Block::STAINED_GLASS:
        case Block::STAINED_GLASS_PANE:
            if($increment == 1){
                $data = $this->nextClothColor($data);
            }else if(increment == -1){
                $this->data = $this->prevClothColor($data);
            }
            return $data;

        default:
            return -1;
        }
    }

    public static function nextClothColor(int $data) : int{
        switch($data){
            case self::WHITE: return self::LIGHT_GRAY;
            case self::LIGHT_GRAY: return self::GRAY;
            case self::GRAY: return self::BLACK;
            case self::BLACK: return self::BROWN;
            case self::BROWN: return self::RED;
            case self::RED: return self::ORANGE;
            case self::ORANGE: return self::YELLOW;
            case self::YELLOW: return self::LIGHT_GREEN;
            case self::LIGHT_GREEN: return self::DARK_GREEN;
            case self::DARK_GREEN: return self::CYAN;
            case self::CYAN: return self::LIGHT_BLUE;
            case self::LIGHT_BLUE: return self::BLUE;
            case self::BLUE: return self::PURPLE;
            case self::PURPLE: return self::MAGENTA;
            case self::MAGENTA: return self::PINK;
            case self::PINK: return self::WHITE;
        }

        return self::WHITE;
    }

    public static function prevClothColor(int $data) : int{
        switch(data){
            case self::LIGHT_GRAY: return self::WHITE;
            case self::GRAY: return self::LIGHT_GRAY;
            case self::BLACK: return self::GRAY;
            case self::BROWN: return self::BLACK;
            case self::RED: return self::BROWN;
            case self::ORANGE: return self::RED;
            case self::YELLOW: return self::ORANGE;
            case self::LIGHT_GREEN: return self::YELLOW;
            case self::DARK_GREEN: return self::LIGHT_GREEN;
            case self::CYAN: return self::DARK_GREEN;
            case self::LIGHT_BLUE: return self::CYAN;
            case self::BLUE: return self::LIGHT_BLUE;
            case self::PURPLE: return self::BLUE;
            case self::MAGENTA: return self::PURPLE;
            case self::PINK: return self::MAGENTA;
            case self::WHITE: return self::PINK;
        }

        return self::WHITE;
    }

    /**
     * Better modulo, not just remainder.
     */
    private static function mod(int $x, int $y) : int{
        $res = $x % $y;
        return $res < 0 ? $res + $y : $res;
    }

        const WHITE = 0;
        const ORANGE = 1;
        const MAGENTA = 2;
        const LIGHT_BLUE = 3;
        const YELLOW = 4;
        const LIGHT_GREEN = 5;
        const PINK = 6;
        const GRAY = 7;
        const LIGHT_GRAY = 8;
        const CYAN = 9;
        const PURPLE = 10;
        const BLUE = 11;
        const BROWN = 12;
        const DARK_GREEN = 13;
        const RED = 14;
        const BLACK = 15;
}