<?php
/**
 * Created by PhpStorm.
 * User: suleymantopaloglu
 * Date: 25.09.18
 * Time: 22:16
 */

namespace _start;

use Controller;
use ReflectionException;
use FETCH_STRUCTURE;
use Config;
use ANLASS;
use ACTIVITY;
use REPOSITORY;
use SHARED_APPLICATION;
use RedirectViewController;

class Home extends Controller
{
        /**
         * Home constructor.
         * @param $pars
         * @param $public_load
         */

        private $club_id = 1;

        /**
         * Home constructor.
         * @param $pars
         * @param $public_load
         * @throws ReflectionException
         */
        function __construct($pars, $public_load)
        {
                parent::__construct($pars, $public_load);
        }

        function getReflectedClass()
        {
                return parent::getReflectedClass(); // TODO: Change the autogenerated stub
        }

        function index()
        {

                // echo constant("ACTIVITY::ACTIVITY_1");

                // /**/echo "Current Device" . highlight_string(var_export($_SESSION[REPOSITORY::CURRENT_DEVICE], true));
                #highlight_string(var_export(REPOSITORY::read(REPOSITORY::CURRENT_USER), true));

                $this->model->pars = $this->pars;
                $ads = $this->model->fetchAllAds();

                $activeAds      = $this->prepareAdds($ads);

                $this->view->setPostDataStructure(FETCH_STRUCTURE::FETCH_OBJECT);
                $this->view->data = array(
                        "ads"           => $activeAds,
                        "imageBasePath" => Config::CLUB_DOCS_BASE_URI . DIRECTORY_SEPARATOR . $this->club_id . DIRECTORY_SEPARATOR . "advertisement" . DIRECTORY_SEPARATOR,

                );

                $adsWithPrettyContent = $this->view->fileContent($this->getReflectedClass()->getShortName() . DIRECTORY_SEPARATOR . $this->getReflectedClass()->getNamespaceName(), "advertisement.php");



                $weatherNow     = $this->weatherNow("Frankenthal");
                $nextMeeting    = $this->fetchNextMeetingWithPrettyRow();
                #highlight_string(var_export($nextMeeting, true));

                //

                $this->view->setPostDataStructure(FETCH_STRUCTURE::FETCH_OBJECT);
                $this->view->data = array(
                        "ads"           => $adsWithPrettyContent,
                        "weather"       => $weatherNow,
                        "nextMeeting"   => $nextMeeting,
                        "redirect"      => $this->redirect()

                );

                $this->view->render($this->getReflectedClass()->getShortname(), $this->getReflectedClass()->getNamespaceName() . DIRECTORY_SEPARATOR . __FUNCTION__ );

        }

        // Redirect
        // Absolute For All Project
        private function redirect(){

                $RVC = new RedirectViewController();


                // Goto Password change
                $me = REPOSITORY::read(REPOSITORY::CURRENT_USER);

                $RVC->autoRoutingWithUserData($me, $isRedirect, $redirectTo);

                if( $isRedirect ){

                        return $redirectTo;

                }

                return null;

        }





        private function prepareAdds($data = array())
        {


                $activeAdds = array();

                if (count($data)) {

                        #highlight_string(var_export($data, true));
                        foreach ($data as $index => $addGroup) {

                                $addGroupAdds = array();
                                for ($i = 0; $i < 3; $i++) {

                                        if ($addGroup["status" . $i]) {

                                                // Required on Javascript
                                                /*
                                                $sharedURL = REPOSITORY::read(REPOSITORY::CURRENT_DEVICE)
                                                        . "//"
                                                        . SHARED_APPLICATION::X2SharedApplicationAdsKey
                                                        . "?embedurl="
                                                        . $addGroup["link" . $i];*/


                                                $gen_data = array();
                                                // Navigation Title
                                                $gen_data["display_name"]              = $addGroup["name" . $i];
                                                // Activity Full Url
                                                $gen_data["link"]                      = $addGroup["link" . $i];
                                                // With Right Bar Button on Navigation
                                                $gen_data[ACTIVITY::ACTIVITY]          = ACTIVITY::go(ACTIVITY::ACTIVITY_SHARED);

                                                $gen_data[SHARED_APPLICATION::X2SharedApplicationKey] = SHARED_APPLICATION::X2SharedApplicationAdsKey;




                                                array_push($addGroupAdds, array(
                                                        "name" => $addGroup["name" . $i],
                                                        "file" => $addGroup["file" . $i],
                                                        "data" => $gen_data,
                                                        "groupId" => $addGroup["id"]
                                                ));
                                        }
                                }

                                if (count($addGroupAdds)) {
                                        array_push($activeAdds, $addGroupAdds);
                                }

                        }
                }

                return $activeAdds;
        }


        private function fetchNextMeetingWithPrettyRow(){


                $this->model->pars = $this->pars;
                $rowData = $this->model->fetchNextMeeting();

                #highlight_string(var_export($rowData, true));

                $data = array();
                // Prepare the Data
                $data["display_name"]                   = $rowData["display_name"];
                $data["title_color"]                    = $rowData["title_color"];
                $data["meeting_pretty_date"]            = $rowData["meeting_pretty_date"];
                $data["link"]                           = Config::BASE_URL . DIRECTORY_SEPARATOR. "Meeting". DIRECTORY_SEPARATOR . "_details" . DIRECTORY_SEPARATOR . "index/?id=" . $rowData["id"];
                $data["icon"]                           = "ic_home";
                $data["last_check"]                     = "";
                $data["unwind_get_data_store"]          = "javascript:new Layout().getUnwindDataStore();";
                // $data["unwind_action"]                  = "javascript:javascript:unwindAction(); new Meeting().updateAvailability();";
                $data[ACTIVITY::ACTIVITY]               = ACTIVITY::go(ACTIVITY::ACTIVITY_2);
                // $data["meeting_id"]                     = $rowData["id"];

                switch ($rowData["anlass"]){

                        case ANLASS::LIGASPIEL:
                        case ANLASS::POKALSPIEL:
                        case ANLASS::TURNIER:
                        case ANLASS::TESTSPIEL:
                                $data["isMatch"] = true;
                                break;

                        default:
                                $data["isMatch"] = false;
                                break;
                }


                $startAbsTime = $rowData["datum"] . " " . $rowData["beginn"];
                // Static declare yap
                $weatherForNextMeeting = $this->weatherIn5DayWithDateAndTime('Frankenthal', $startAbsTime );
                
                // highlight_string(var_export($rowData, true));

                if( count($rowData) ){

                        $this->view->setPostDataStructure(FETCH_STRUCTURE::FETCH_ARRAY);
                        $this->view->data = array(
                            "post"          => $data,
                            "db"            => $rowData,
                            "weather"       => $weatherForNextMeeting
                        );


                        $row = $this->view->fileContent("Meeting" . DIRECTORY_SEPARATOR . "_list", "cell.php");
                        return $row;


                }

                return NULL;




        }





        public function weatherNow( $city )
        {
                return parent::weatherNow( $city ); // TODO: Change the autogenerated stub
        }

        public function weatherIn5DayWithDateAndTime($city, $datetime)
        {
                return parent::weatherIn5DayWithDateAndTime($city, $datetime); // TODO: Change the autogenerated stub
        }


        function __destruct()
        {
                parent::__destruct(); // TODO: Change the autogenerated stub
        }

}