<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Infrastructure;

final class RestApiFailure extends \RuntimeException
{
    public static function fromPrevious(\Exception $e)
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
