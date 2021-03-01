<?php
namespace foo;
class A {
    protected $namespace = __NAMESPACE__;
    public function factory() {
        $c = $this->namespace . '\C';
        return new $c;
    }
}
class C {
    public function tell() {
        echo "foo";
    }
}