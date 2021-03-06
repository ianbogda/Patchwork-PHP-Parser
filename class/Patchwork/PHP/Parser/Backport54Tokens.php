<?php /****************** vi: set fenc=utf-8 ts=4 sw=4 et: *****************
 *
 *   Copyright : (C) 2012 Nicolas Grekas. All rights reserved.
 *   Email     : p@tchwork.org
 *   License   : http://www.gnu.org/licenses/lgpl.txt GNU/LGPL
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.
 *
 ***************************************************************************/

defined('T_TRAIT') || Patchwork_PHP_Parser::createToken('T_TRAIT');
defined('T_TRAIT_C') || Patchwork_PHP_Parser::createToken('T_TRAIT_C');
defined('T_CALLABLE') || Patchwork_PHP_Parser::createToken('T_CALLABLE');
defined('T_INSTEADOF') || Patchwork_PHP_Parser::createToken('T_INSTEADOF');

/**
 * The Backport54Tokens parser backports tokens introduced in PHP 5.4.
 */
class Patchwork_PHP_Parser_Backport54Tokens extends Patchwork_PHP_Parser
{
    protected $backports = array(
        'trait' => T_TRAIT,
        'callable' => T_CALLABLE,
        '__trait__' => T_TRAIT_C,
        'insteadof' => T_INSTEADOF,
    );

    protected function getTokens($code)
    {
        foreach ($this->backports as $k => $i)
            if (self::T_OFFSET >= $i || false === stripos($code, $k))
                unset($this->backports[$k]);

        $code = parent::getTokens($code);
        $i = 0;

        if ($this->backports)
            while (isset($code[++$i]))
                if (T_STRING === $code[$i][0] && isset($this->backports[strtolower($code[$i][1])]))
                    $code[$i][0] = $this->backports[strtolower($code[$i][1])];

        return $code;
    }
}
