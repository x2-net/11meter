<?php
/**
 * Created by PhpStorm.
 * User: suleymantopaloglu
 * Date: 17.10.18
 * Time: 09:16
 */
namespace monthview;
use _list\Meeting_Model as Meetings;
use Model;
use Database;
class Calendar_Model extends Model
{
        function __construct()
        {
                parent::__construct();
        }


        public function connect()
        {
                // TODO: Implement connect() method.
                $this->db = new Database();
                

        }

        function fetchMonth(){

                include "Models/Meeting_Model.php";


                $this->pars->queryCustomStatement = "MONTH(datum)=" . $this->pars->month . " AND YEAR(datum)=" . $this->pars->year ;

                $meetings = new Meetings();

                $meetings->pars = $this->pars;
                return $meetings->fetchAll();

        }








        public function __destruct()
        {
                parent::__destruct(); // TODO: Change the autogenerated stub
        }
}

namespace weekview;
use Model;
class Calendar_Model extends Model{

        public function __construct()
        {
                parent::__construct();
        }

        public function connect()
        {
                parent::connect(); // TODO: Change the autogenerated stub
        }


}



namespace dayview;
use Model;
class Calendar_Model extends Model{

        public function __construct()
        {
                parent::__construct();
        }

        public function connect()
        {
                parent::connect(); // TODO: Change the autogenerated stub
        }

        function __destruct()
        {
                parent::__destruct(); // TODO: Change the autogenerated stub
        }


}