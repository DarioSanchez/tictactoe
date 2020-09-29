<?php
namespace app\http\controller;
require '../../../autoload.php';
use app\models\TicTacToe;


class TicTacToeController extends Game implements GameInterface
{
    protected $tictactoe;

    public function __construct(TicTacToe $t)
    {
        $this->tictactoe = new TicTacToe();
    }
    /*
    *  Check for winner letter and create records on activity
    * @response json
    */
    public function checkForWinner()
    {
        $obj = new \stdClass();
        $obj->params = request();
        createSession();
        sessionUser();
        //create record in table records
        $this->tictactoe->createRecords( json_encode($obj->params) );
        print_r(json_encode($this->isWinner()));
    }
    /*
    *  Check for winner letter
    * @response array
    */
    public function isWinner()
    {
        $obj = new \stdClass();
        $obj->records = json_decode($this->tictactoe->getRegistry()['registry'],true);
        for ($i=0;$i<sizeof($obj->records);$i++) {
            if($obj->records[$i]['value'] === "O"){
                $arr['O'][] = $obj->records[$i]['key'];
            } else {
                $arr['X'][] = $obj->records[$i]['key'];
            }
        }
        //we look in the patterns for matches [0,1,2] = [0,1,2] -> WINNER
        $isWinnerO = array_search($arr['O'], TicTacToe::WINNING_COMBINATIONS);
        if ($isWinnerO === false) {
            $isWinnerO = array_search(array_reverse($arr['O']), TicTacToe::WINNING_COMBINATIONS);
        }
        $isWinnerX = array_search($arr['X'], TicTacToe::WINNING_COMBINATIONS);
        if ($isWinnerX === false) {
            $isWinnerX = array_search(array_reverse($arr['X']), TicTacToe::WINNING_COMBINATIONS);
        }
        if ($isWinnerO !== false || $isWinnerO === 0) {
             $result = array( 'result' => 'I win letter O', 'winner' => true );
            //regenerate session user
            regenerateSession();
        } elseif ($isWinnerX !== false || $isWinnerX === 0) {
             $result = array( 'result' => 'I win letter X', 'winner' => true );
            //regenerate session user
             regenerateSession();
        } else {
            regenerateSessionForMaxRecords($obj->records);
            $result = array( 'result' => null, 'winner' => false );
        }
        return $result;
    }
}
$game = new TicTacToeController(new TicTacToe());
$game->checkForWinner();