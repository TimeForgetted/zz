<?php
/**
 * Created by xuyi
 */

namespace framework\components\validate\rule;


class EmailRule {
    public function valid($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}