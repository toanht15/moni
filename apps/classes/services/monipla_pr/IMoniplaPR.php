<?php

interface IMoniplaPR {
    
    public function isMine(Cp $cp, CpUser $cp_user);

    public function parseTemplate(Cp $cp, CpUser $cp_user);
    
}