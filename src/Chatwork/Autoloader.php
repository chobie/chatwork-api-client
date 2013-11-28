<?php
namespace Chatwork;

/**
 * Chatwork API Client
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2013, Shuhei Tanuma. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class Autoloader
{
    const NAME_SPACE = 'Chatwork';

    protected static $base_dir;

    /**
     * register autoloader
     *
     * @param string $dirname base directory path.
     * @return void
     */
    public static function register($dirname = null)
    {
        if (is_null($dirname)) {
            $dirname = dirname(__FILE__);
        }
        self::$base_dir = $dirname;
        spl_autoload_register(array(__CLASS__, "autoload"));
    }

    /**
     * unregister autoloader
     *
     * @return void
     */
    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, "autoload"));
    }

    /**
     * autoloader implementation
     *
     * @param string $name class name
     * @return boolean return true when load successful
     */
    public static function autoload($name)
    {
        $retval = false;

        if (strpos($name,self::NAME_SPACE) === 0) {
            $parts = explode("\\", $name);
            array_shift($parts);

            $expected_path = join(DIRECTORY_SEPARATOR, array(
                self::$base_dir,
                join(DIRECTORY_SEPARATOR,$parts) . ".php"
            ));

            if (is_file($expected_path) && is_readable($expected_path)) {
                require $expected_path;
                $retval = true;
            }
        }

        return $retval;
    }
}