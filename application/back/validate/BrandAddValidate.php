<?php
namespace app\back\validate;

use think\Validate;
class BrandAddValidate extends Validate
{
    protected $rule = [
        'title' => ['require', ],
        'site' => ['url'],
        'logo'=>['require'],
        'sort' => ['require', 'number']
    ];
}