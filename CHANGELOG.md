# Changelog

## 5.1.0 - 2024-12-17

### Changed

* Issue [#501](https://github.com/dotkernel/frontend/issues/501): Home page cleanup by [@alexmerlin](https://github.com/alexmerlin) in [#502](https://github.com/dotkernel/frontend/pull/502)
* Issue [#505](https://github.com/dotkernel/frontend/issues/505): Replaced `Psalm` with `PHPStan` by [@MarioRadu](https://github.com/MarioRadu) in [#506](https://github.com/dotkernel/frontend/pull/506)
* Issue [#515](https://github.com/dotkernel/frontend/issues/515): Updated `PHPStan` command by [@bidi47](https://github.com/bidi47) in [#516](https://github.com/dotkernel/frontend/pull/516)
* Issue [#509](https://github.com/dotkernel/frontend/issues/509): Updated `dotkernel/dot-mail` package by [@MarioRadu](https://github.com/MarioRadu) in [#518](https://github.com/dotkernel/frontend/pull/518)
* Issue [#524](https://github.com/dotkernel/frontend/issues/524): Updated `laminas/laminas-coding-standard` to latest version by [@MarioRadu](https://github.com/MarioRadu) in [#525](https://github.com/dotkernel/frontend/pull/525)
* Issue [#526](https://github.com/dotkernel/frontend/issues/526): Bumped `dotkernel/dot-mail` version to 5.1 by [@MarioRadu](https://github.com/MarioRadu) in [#527](https://github.com/dotkernel/frontend/pull/527)
* Update qodana_code_quality.yml by [@arhimede](https://github.com/arhimede) in [#503](https://github.com/dotkernel/frontend/pull/503)

### Added

* Issue [#507](https://github.com/dotkernel/frontend/issues/507): Implemented enums in database by [@alexmerlin](https://github.com/alexmerlin) in [#508](https://github.com/dotkernel/frontend/pull/508)
* Issue [#513](https://github.com/dotkernel/frontend/issues/513): Added static analysis file by [@MarioRadu](https://github.com/MarioRadu) in [#514](https://github.com/dotkernel/frontend/pull/514)
* Issue [#523](https://github.com/dotkernel/frontend/issues/523): Add the status "deleted" to the user table by [@bidi47](https://github.com/bidi47) in [#529](https://github.com/dotkernel/frontend/pull/529)
* Issue [#526](https://github.com/dotkernel/frontend/issues/526): Composer post install script by [@MarioRadu](https://github.com/MarioRadu) in [#528](https://github.com/dotkernel/frontend/pull/528)

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Issue [#520](https://github.com/dotkernel/frontend/issues/520): Restricted `Qodana` to supported PHP versions by [@alexmerlin](https://github.com/alexmerlin) in [#521](https://github.com/dotkernel/frontend/pull/521)
* Issue [#526](https://github.com/dotkernel/frontend/issues/526): Environment composer post install script bug fix by [@MarioRadu](https://github.com/MarioRadu) in [#530](https://github.com/dotkernel/frontend/pull/530)

## 5.0.1 - 2024-10-03

### Changed

* Nothing

### Added

* Issue [#487](https://github.com/dotkernel/frontend/issues/487): Created file `CHANGELOG.md` by [@alexmerlin](https://github.com/alexmerlin) in [#488](https://github.com/dotkernel/frontend/pull/488)
* Added .gitattributes by [@bidi47](https://github.com/bidi47) in [#489](https://github.com/dotkernel/frontend/pull/489)

### Deprecated

* Nothing

### Removed

* Issue [#493](https://github.com/dotkernel/frontend/issues/493): Removed unused assets by [@alexmerlin](https://github.com/alexmerlin) in [#496](https://github.com/dotkernel/frontend/pull/496)
* Issue [#494](https://github.com/dotkernel/frontend/issues/494): Removed `rector/rector` package and config file by [@alexmerlin](https://github.com/alexmerlin) in [#495](https://github.com/dotkernel/frontend/pull/495)
* Issue [#497](https://github.com/dotkernel/frontend/issues/497): Temporarily remove `.gitattributes` file to repair `CRLF` files by [@alexmerlin](https://github.com/alexmerlin) in [#499](https://github.com/dotkernel/frontend/pull/499)

### Fixed

* Issue [#484](https://github.com/dotkernel/frontend/issues/484): Show correct message when user tries to register with a deleted account email by [@alexmerlin](https://github.com/alexmerlin) in [#491](https://github.com/dotkernel/frontend/pull/491)
* Issue [#498](https://github.com/dotkernel/frontend/issues/498): Fixed `CRLF` files and restored `.gitattributes` by [@alexmerlin](https://github.com/alexmerlin) in [#500](https://github.com/dotkernel/frontend/pull/500)

## 5.0.0 - 2024-09-16

### Changed

* Issue [#440](https://github.com/dotkernel/frontend/issues/440): Replaced annotations with attributes by [@MarioRadu](https://github.com/MarioRadu) in [#446](https://github.com/dotkernel/frontend/pull/446)
* Issue [#427](https://github.com/dotkernel/frontend/issues/427): Updated readme by [@bidi47](https://github.com/bidi47) in [#448](https://github.com/dotkernel/frontend/pull/448)
* Issue [#447](https://github.com/dotkernel/frontend/issues/447): Updated logo by [@bidi47](https://github.com/bidi47) in [#457](https://github.com/dotkernel/frontend/pull/457)
* Issue [#459](https://github.com/dotkernel/frontend/issues/459): Updated composer by [@bidi47](https://github.com/bidi47) in [#461](https://github.com/dotkernel/frontend/pull/461)
* Issue [#469](https://github.com/dotkernel/frontend/issues/469): Refactored twig templates by [@cPintiuta](https://github.com/cPintiuta) in [#471](https://github.com/dotkernel/frontend/pull/471)
* Issue [#473](https://github.com/dotkernel/frontend/issues/473): Updated contact us by [@bidi47](https://github.com/bidi47) in [#474](https://github.com/dotkernel/frontend/pull/474)
* Issue [#470](https://github.com/dotkernel/frontend/issues/470): Upgraded `dot-errorhandler` to version `4.x` by [@alexmerlin](https://github.com/alexmerlin) in [#476](https://github.com/dotkernel/frontend/pull/476)
* Issue [#444](https://github.com/dotkernel/frontend/issues/444): Updated JS dependencies. by [@alexmerlin](https://github.com/alexmerlin) in [#477](https://github.com/dotkernel/frontend/pull/477)
* Issue [#439](https://github.com/dotkernel/frontend/issues/439): Updated webpack config - fonts fix by [@bidi47](https://github.com/bidi47) in [#475](https://github.com/dotkernel/frontend/pull/475)
* Issue [#479](https://github.com/dotkernel/frontend/issues/479): Updated account delete by [@bidi47](https://github.com/bidi47) in [#480](https://github.com/dotkernel/frontend/pull/480)
* Issue [#485](https://github.com/dotkernel/frontend/issues/485): Preparation for v5 release. by [@alexmerlin](https://github.com/alexmerlin) in [#486](https://github.com/dotkernel/frontend/pull/486)

### Added

* Issue [#342](https://github.com/dotkernel/frontend/issues/342): Implemented `CSRF` protection in all forms by [@alexmerlin](https://github.com/alexmerlin) in [#454](https://github.com/dotkernel/frontend/pull/454)
* Issue [#432](https://github.com/dotkernel/frontend/issues/432): Added twig cs by [@bidi47](https://github.com/bidi47) in [#463](https://github.com/dotkernel/frontend/pull/463)
* Issue [#441](https://github.com/dotkernel/frontend/issues/441): Implemented doctrine orm 3 by [@cPintiuta](https://github.com/cPintiuta) in [#460](https://github.com/dotkernel/frontend/pull/460)
* Issue [#462](https://github.com/dotkernel/frontend/issues/462): Canonical url by [@bidi47](https://github.com/bidi47) in [#464](https://github.com/dotkernel/frontend/pull/464)
* Issue [#482](https://github.com/dotkernel/frontend/issues/482): Account delete - avatar by [@bidi47](https://github.com/bidi47) in [#483](https://github.com/dotkernel/frontend/pull/483)

### Deprecated

* Nothing

### Removed

* Issue [#449](https://github.com/dotkernel/frontend/issues/449): Removed `dot-debugbar` integration by [@alexmerlin](https://github.com/alexmerlin) in [#450](https://github.com/dotkernel/frontend/pull/450)
* Issue [#445](https://github.com/dotkernel/frontend/issues/445): Removed translation feature by [@bidi47](https://github.com/bidi47) in [#468](https://github.com/dotkernel/frontend/pull/468)

### Fixed

* Issue [#455](https://github.com/dotkernel/frontend/issues/455): Fixed JS error on pages where `.kv-fileinput-caption` is not present by [@alexmerlin](https://github.com/alexmerlin) in [#456](https://github.com/dotkernel/frontend/pull/456)
* Issue [#453](https://github.com/dotkernel/frontend/issues/453): Issue 453 by [@bidi47](https://github.com/bidi47) in [#478](https://github.com/dotkernel/frontend/pull/478)
* Replace branch 4 with 5 in qodana by [@arhimede](https://github.com/arhimede) in [#481](https://github.com/dotkernel/frontend/pull/481)
