<?php
/**
 * Created by xuyi
 */

namespace framework\components\validate\rule;


abstract class Rule {
    abstract function valid($value);
}