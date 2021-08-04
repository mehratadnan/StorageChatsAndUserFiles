<?php

namespace App\Http\Controllers;

use App\Providers\JsonServiceProvider;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected $response;

    /**
     * Controller constructor.
     */
    public function __construct(){
        $this->response = new JsonServiceProvider();
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Support\MessageBag
     */
    protected function checkValidator(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make(
            $request->all(),
            $rules, $messages,
            $customAttributes
        );

        if ($validator->fails()) {
            return $validator->errors();
        }
    }



}
