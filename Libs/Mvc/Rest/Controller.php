<?php namespace Ovide\Libs\Mvc\Rest;

/**
 * Description of RestController
 * @author Albert Ovide <albert@ovide.net>
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{

    const OK              = 200;
    const CREATED         = 201;
    const ACCEPTED        = 202;
    const NO_CONTENT      = 204;

    const MOVED           = 301;
    const NOT_MODIFIED    = 304;

    const BAD_REQUEST     = 400;
    const UNAUTHORIZED    = 401;
    const FORBIDDEN       = 403;
    const NOT_FOUND       = 404;
    const NOT_ALLOWED     = 405;
    const NOT_ACCEPTABLE  = 406;
    const CONFLICT        = 409;
    const GONE            = 410;
    const UNSUPPORTED_MT  = 415;

    const INTERNAL_ERROR  = 500;
    const NOT_IMPLEMENTED = 501;
    const UNAVAILABLE     = 502;

    public static $status = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',

        301 => 'Moved Permanently',
        304 => 'Not Modified',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method not allowed',
        406 => 'Not Acceptable',
        409 => 'Conflict',
        410 => 'Gone',
        415 => 'Unsupported media type',

        500 => 'Internal server error',
        501 => 'Not implemented',
        503 => 'Service unavailable',
    );


    public function index($id=null)
    {
        $this->response->resetHeaders();
        $this->response->setContent('');
        $method = $this->request->getMethod();
        try {
            $this->_call($method, $id);
        } catch (\Exception $ex) {
            $this->response([
                'message' => $ex->getMessage(),
                'code'    => $ex->getCode(),
            /* [
                'type'    => \get_class($ex),
                'file'    => $ex->getFile(),
                'line'    => $ex->getLine()
             * 
             */
            ], ($ex instanceof Exception\Exception) ? $ex->getCode() : self::INTERNAL_ERROR);
        }
        return $this->response;
    }
    
    protected function _call($method, $id)
    {
        switch ($method) {
            case 'GET':
                if ($id === '') {
                    $this->_get();
                } else {
                    $this->_getOne($id);
                }
                break;
            case 'POST':
                $this->_post();
                break;
            case 'PUT':
                $this->_put($id);
                break;
            case 'DELETE':
                $this->_delete($id);
                break;
            default:
                $this->response(['message' => 'Method not allowed'], self::NOT_ALLOWED);
        }        
    }

    protected function response($content=null, $code=null, $message=null)
    {
        if ($content) {
            if ($code === null || $code < 300) {
                $ret = $this->eTag($content);
                if ($ret) {
                    return;
                }
            }
            self::buildBody($this->response, $content);
            if (!$code) {
                $code = self::OK;
            }
        } else if (!$code) {
            $code = self::NO_CONTENT;
        }
        if (!$message) {
            $message = self::$status[$code];
        }
        $this->response->setStatusCode($code, $message);
    }
    
    protected function eTag($content)
    {
        $rq = $_SERVER;
        $et = $this->request->getHeader('HTTP_IF_NONE_MATCH');
        $net = md5(serialize($content));
        $this->response->setHeader('ETag', $net);
        if ($et === $net) {
            $this->response->setStatusCode(self::NOT_MODIFIED, self::$status[self::NOT_MODIFIED]);
            return true;
        }
        return false;
    }

    protected static function buildBody(\Phalcon\HTTP\ResponseInterface &$rsp, $content)
    {
        $rsp->setContentType('application/json');
        $rsp->setJsonContent($content);
    }
    
    public static function notFound(\Phalcon\HTTP\ResponseInterface &$rsp)
    {
        $rsp->setStatusCode(self::NOT_FOUND, self::$status[self::NOT_FOUND]);
        self::buildBody($rsp, [
            'message' => self::$status[self::NOT_FOUND],
            'code'    => self::NOT_FOUND
        ]);
    }

    protected function _getOne($id)
    {
        $this->response($this->getOne($id));
    }

    protected function _get()
    {
        $this->response($this->get());
    }

    protected function _post()
    {
        $obj = $this->request->getPost();
        $this->response($this->post($obj), self::CREATED);
    }

    protected function _put($id)
    {
        $obj = $this->request->getPost();
        $this->response($this->put($id, $obj));
    }

    protected function _delete($id)
    {
        $this->delete($id);
        $this->response(null);
    }
}
