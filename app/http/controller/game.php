<?php 
namespace app\http\controller;

use app\http\controller\gameInterface;

abstract class Game
{
    abstract public function checkForWinner();
}