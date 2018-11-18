<?php
defined('JETEE_PATH') or exit();

class TokenBuildBehavior extends Behavior {

    public function run(&$content){
		buildToken();
    }

}