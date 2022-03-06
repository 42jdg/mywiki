<?php

namespace OCA\MyWiki\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\MyWiki\Service\NotFoundException;
use OCA\MyWiki\Service\ReadOnlyException;


trait Errors {

    protected function handleNotFound (Closure $callback) {
        try {
            return new DataResponse($callback());
        } catch(NotFoundException $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }
    protected function handleReadOnly (Closure $callback) {
        try {
            return new DataResponse($callback());
        } catch(ReadOnlyException $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }

}