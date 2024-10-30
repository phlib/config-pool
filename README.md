# phlib/config-pool

[![Code Checks](https://img.shields.io/github/actions/workflow/status/phlib/config-pool/code-checks.yml?logo=github)](https://github.com/phlib/config-pool/actions/workflows/code-checks.yml)
[![Codecov](https://img.shields.io/codecov/c/github/phlib/config-pool.svg?logo=codecov)](https://codecov.io/gh/phlib/config-pool)
[![Latest Stable Version](https://img.shields.io/packagist/v/phlib/config-pool.svg?logo=packagist)](https://packagist.org/packages/phlib/config-pool)
[![Total Downloads](https://img.shields.io/packagist/dt/phlib/config-pool.svg?logo=packagist)](https://packagist.org/packages/phlib/config-pool)
![Licence](https://img.shields.io/github/license/phlib/config-pool.svg)

Used for consistent hashing a pool of configs

```php
$config = [
    'server1' => ['hostname' => 'localhost', 'port' => 11211],
    'server2' => ['hostname' => 'localhost', 'port' => 11212],
    'server3' => ['hostname' => 'localhost', 'port' => 11213],
];
$pool = new ConfigPool($config);
var_dump($pool->getConfigList('some key', 2));
```

## License

This package is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
