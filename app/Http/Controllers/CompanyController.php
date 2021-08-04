<?php


namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class companyController extends Controller
{
    /**
     * @param $request
     * @return JsonResponse
     */

    public function companyChatOperations(Request $request): JsonResponse
    {
        $checkvalidation = $this->checkRequest($request, "companyChatOperations");
        if ($checkvalidation === true) {
            if($this->checkConnection($request->access) === true){
                $companyID =  $request->companyID;
                $companyPath = 'Storage/Companies/Company_' . $companyID;
                $action = $request->action;
                $channel = $request->channel;
                $operation = $request->operation;
                $files = $request->file('files');

                $year = date("Y");
                $month = date("m");
                $day = date("d");

                $this->folderCreater( $companyPath . '/' . $action . '/' . $channel . '/' . $year . '/' . $month . '/' . $day . '/' . $operation);

                $filesPath = array();
                foreach($files as $file) {
                    $microtime = str_replace(".", "_", microtime());
                    $microtime = str_replace(" ", "_", $microtime);
                    $name = $companyID.'_'.$channel.'_'.date("Y_m_d").'_'.$microtime;
                    $file->move($companyPath . '/' . $action . '/' . $channel . '/' . $year . '/' . $month . '/' . $day . '/' . $operation,$name.'.'.$file->clientExtension());
                    array_push($filesPath,$companyPath . '/' . $action . '/' . $channel . '/' . $year . '/' . $month . '/' . $day . '/' . $operation. '/' . $name.'.'.$file->clientExtension());
                }
                return $this->response->success(["data" => $filesPath ,"code" => 200,"count" => count($filesPath)]);
            }else{
                return $this->response->success(["data" => "","error" => 'Access denied' ,"code" => 200]);
            }
        }else{
            return $this->response->fail($checkvalidation);
        }
    }



    /**
     * @param $request
     * @return JsonResponse
     */
    public function companyUserOperations(Request $request): JsonResponse
    {
        $checkvalidation = $this->checkRequest($request, "companyUserOperations");
        if ($checkvalidation === true) {
            if($this->checkConnection($request->access) === true){

                $companyID =  $request->companyID;
                $companyPath = 'Storage/Companies/Company_' . $companyID;
                $action = $request->action;
                $userID = $request->userID;
                $files = $request->file('files');

                $this->folderCreater( $companyPath . '/' . $action . '/user' . $userID );

                $filesPath = array();
                foreach($files as $file) {
                    $microtime = str_replace(" ", "_", str_replace(".", "_", microtime())) ;
                    $name = $companyID.'_user'.$userID.'_'.date("Y_m_d").'_'.$microtime;
                    $file->move($companyPath . '/' . $action . '/user_' . $userID ,$name.'.'.$file->clientExtension());
                    array_push($filesPath,$companyPath . '/' . $action . '/user_' . $userID .'/' . $name.'.'.$file->clientExtension());
                }
                return $this->response->success(["data" => $filesPath ,"code" => 200,"count" => count($filesPath)]);
            }else{
                return $this->response->success(["data" => "","error" => 'Access denied' ,"code" => 200]);
            }
        }else{
            return $this->response->fail($checkvalidation);
        }
    }


    /**
     * @param $path
     */
    private function folderCreater($path)
    {
        try {
            if(!file_exists($path)){
                mkdir($path, 0777, true);
                return true;
            }
        } catch (Exception $exception) {
            return $this->response->success(["data" => $exception,"error" => $path.' folder can not be created' ,"code" => 200]);
        }
    }


    /**
     * @param $request
     * @param $ctrl
     */
    private function checkRequest($request,$ctrl)
    {
        //Request Cleaning
        foreach ($request->all() as $key => $value) {
            if(!is_array($request[$key])){
                $request[$key] = trim(strip_tags($request[$key]));
            }
        }

        if($ctrl == "companyChatOperations"){
            //Request Validator
            $validate = $this->checkValidator($request, [
                'companyID' => 'required',  //id of company
                'action' => 'required',     //chats
                'channel' => 'required',    //facebook or whatsapp .....
                'operation' => 'required',  //sounds or files ....
                'files' => '',              //file or files  ....
                'access' => 'required',     //password  ....
            ]);
        }else{
            //Request Validator
            $validate = $this->checkValidator($request, [
                'companyID' => 'required',  //id of company
                'action' => 'required',     //users
                'userID' => 'required',     //facebook or whatsapp .....
                'files' => '',              //file or files  ....
                'access' => 'required',     //password  ....
            ]);
        }

        if (!empty($validate)) {
            return $validate;
        } else {
            return true;
        }
    }


    /**
     * @param $check
     * @return bool
     */
    private function checkConnection($check): bool
    {
        $check = trim(strip_tags($check));
        if($_ENV['SERVER'] == $check) {
            return true;
        }else{
            return false;
        }
    }

}



