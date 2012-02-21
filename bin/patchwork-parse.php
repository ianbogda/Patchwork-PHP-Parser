#!/usr/bin/env php
<?php

ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('error_log', 'php://stderr');
error_reporting(E_ALL | E_STRICT);
function_exists('xdebug_disable') and xdebug_disable();

function __autoload($class)
{
    $class = str_replace(array('\\', '_'), array('/', '/'), $class);
    require dirname(__DIR__) . '/class/' . $class . '.php';
}

class Preprocessor
{
    static function getParser($file)
    {
        $parser = new Patchwork_PHP_Parser_Normalizer();
        $parser = new Patchwork_PHP_Parser_ShortOpenEcho($parser);
        new Patchwork_PHP_Parser_BracketBalancer($parser);
        new Patchwork_PHP_Parser_CurlyDollarNormalizer($parser);
        new Patchwork_PHP_Parser_ShortArray($parser);
        $parser = new Patchwork_PHP_Parser_BinaryNumber($parser);
        new Patchwork_PHP_Parser_StringInfo($parser);
        $parser = new Patchwork_PHP_Parser_Backport54Tokens($parser);
        new Patchwork_PHP_Parser_Backport53Tokens($parser);
        new Patchwork_PHP_Parser_NamespaceInfo($parser);
        new Patchwork_PHP_Parser_ScopeInfo($parser);
        new Patchwork_PHP_Parser_ConstantInliner($parser, $file);
        new Patchwork_PHP_Parser_ClassInfo($parser);

        return $parser;
    }
}

$file = isset($argv[1]) ? realpath($argv[1]) : null;
$code = file_get_contents('php://stdin');

$parser = Preprocessor::getParser($file);
$code = $parser->parse($code);

if ($e = $parser->getErrors())
{
    foreach ($e as $e)
    {
        switch ($e['type'])
        {
        case 0: continue 2;
        case E_USER_NOTICE;
        case E_USER_WARNING;
        case E_USER_DEPRECATED; break;
        default:
        case E_ERROR: $e['type'] = E_USER_ERROR; break;
        case E_NOTICE: $e['type'] = E_USER_NOTICE; break;
        case E_WARNING: $e['type'] = E_USER_WARNING; break;
        case E_DEPRECATED: $e['type'] = E_USER_DEPRECATED; break;
        }

        user_error("{$e['message']} on line {$e['line']} as parsed by {$e['parser']}", $e['type']);
    }
}

echo $code;