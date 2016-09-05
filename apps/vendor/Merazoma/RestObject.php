<?php
namespace Merazoma;
interface RestObject {
    public function get ();
    public function post ();
    public function delete ();
    public function put ();
}