<?php
interface IContainerBase extends Iterator {
  public function map ( $f );
  public function filter ( $f );
}
