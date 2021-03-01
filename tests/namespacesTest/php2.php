<?php
namespace bar;
class B extends \foo\A {
    protected $namespace = __NAMESPACE__;
}
class C {
    public function tell() {
        echo "bar";
    }
}