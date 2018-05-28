<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Solarium\Cloud;

use Solarium\Cloud\Core\Client\CloudClient;

/**
 * This class makes the client easier to use (shorter class name) and adds
 * a library version check.
 */
class Client extends CloudClient
{
    /**
     * Version number of the Solarium library.
     *
     * The version is built up in this format: major.minor.mini
     *
     * A major release is used for significant release with architectural
     * changes and changes that might break backwards compatibility
     *
     * A minor release adds and enhances features, and might also contain
     * bugfixes. It should be backwards compatible, or the incompatibilities
     * should be clearly documented with the release.
     *
     * A mini release only contains bugfixes to existing features and is always
     * backwards compatible.
     *
     * If you develop your application to a specific Solarium version it is best
     * to check for that exact major and minor version, leaving the mini version
     * open to allow for upgrades in case of bugfixes.
     *
     * @see checkExact()
     * @see checkMinimal()
     *
     * @var string
     */
    const VERSION = '0.2.0';

    /**
     * Check for an exact version.
     *
     * This method can check for all three versioning levels, but they are
     * optional. If you only care for major and minor versions you can use
     * something like '1.0' as input. Or '1' if you only want to check a major
     * version.
     *
     * For each level that is checked the input has to be exactly the same as
     * the actual version. Some examples:
     *
     * The if the version is 1.2.3 the following checks would return true:
     * - 1 (only major version is checked)
     * - 1.2 (only major and minor version are checked)
     * - 1.2.3 (full version is checked)
     *
     * These values will return false:
     * - 1.0 (lower)
     * - 1.2.4 (higher)
     *
     *
     * A string compare is used instead of version_compare because
     * version_compare returns false for a compare of 1.0.0 with 1.0
     *
     * @param string $version
     *
     * @return boolean
     */
    public static function checkExact($version): bool
    {
        return (strpos(self::VERSION, $version) === 0);
    }

    /**
     * Check for a minimal version.
     *
     * This method can check for all three versioning levels, but they are
     * optional. If you only care for major and minor versions you can use
     * something like '1.0' as input. Or '1' if you only want to check a major
     * version.
     *
     * For each level that is checked the actual value needs to be the same or
     * higher. Some examples:
     *
     * The if the version is 1.2.3 the following checks would return true:
     * - 1.2.3 (the same)
     * - 1 (the actual version is higher)
     *
     * These values will return false:
     * - 2 (the actual version is lower)
     * - 1.3 (the actual version is lower)
     *
     * @param string $version
     *
     * @return boolean
     */
    public static function checkMinimal($version): bool
    {
        return version_compare(self::VERSION, $version, '>=');
    }
}
