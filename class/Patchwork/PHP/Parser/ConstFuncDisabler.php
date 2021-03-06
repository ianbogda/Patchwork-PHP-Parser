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

// TODO: allow local usage of inline declared consts, functions and define()

/**
 * The ConstFuncDisabler parser emits a deprecation notice on namespaced functions or constants declarations.
 */
class Patchwork_PHP_Parser_ConstFuncDisabler extends Patchwork_PHP_Parser
{
    protected

    $callbacks = array('tagOpenTag' => T_SCOPE_OPEN),

    $scope, $namespace,
    $dependencies = array('ScopeInfo' => array('scope', 'namespace'));


    protected function tagOpenTag(&$token)
    {
        if (T_NAMESPACE === $this->scope->type && $this->namespace)
        {
            $this->register($this->callbacks = array(
                'tagConstFunc'  => array(T_NAME_FUNCTION, T_NAME_CONST),
                'tagScopeClose' => T_BRACKET_CLOSE,
            ));
        }
    }

    protected function tagConstFunc(&$token)
    {
        if (T_CLASS !== $this->scope->type && T_INTERFACE !== $this->scope->type && T_TRAIT !== $this->scope->type)
        {
            $this->setError("Namespaced functions and constants are deprecated, please use class constants and static methods instead", E_USER_DEPRECATED);
        }
    }

    protected function tagScopeClose(&$token)
    {
        $this->unregister($this->callbacks);
    }
}
