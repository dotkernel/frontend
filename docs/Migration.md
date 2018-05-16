DotKernel3 Migration
---

Migration from Zend Expressive 2 to 3.

In `composer.json` replace the matching repositories with the following:

```  
"dotkernel/dot-authentication-service":"^1.0",
"dotkernel/dot-authentication-web":"^1.0.1",
"dotkernel/dot-controller":"^1.0",
"dotkernel/dot-controller-plugin-authentication":"^1.0",
"dotkernel/dot-controller-plugin-authorization":"^1.0",
"dotkernel/dot-controller-plugin-flashmessenger":"^1.0",
"dotkernel/dot-controller-plugin-forms":"^1.0",
"dotkernel/dot-controller-plugin-mail":"^1.0",
"dotkernel/dot-controller-plugin-session":"^1.0",
"dotkernel/dot-flashmessenger":"^1.0",
"dotkernel/dot-helpers":"^1.0",
"dotkernel/dot-mail":"^1.0",
"dotkernel/dot-mapper":"^1.0",
"dotkernel/dot-navigation":"^1.0",
"dotkernel/dot-rbac":"^0.2.1",
"dotkernel/dot-rbac-guard":"dev-migrate",
"dotkernel/dot-session":"^3.0",
"dotkernel/dot-twigrenderer":"^1.1",
"dotkernel/dot-user":"^1.0",

"zendframework/zend-stratigility": "^3.0",
"zendframework/zend-expressive": "^3.0",
"zendframework/zend-expressive-router": "^3.0",
"zendframework/zend-expressive-fastroute": "^3.0",
"zendframework/zend-expressive-tooling": "^1.0",
```
also update require-dev dependencies
```
"zendframework/zend-expressive-tooling:": "^1.0",
"zendframework/zend-component-installer": "^2.0",

```

Remove packages: 
* http-interop/http-middleware 
* webimpress/http-middleware-compatibility
```
composer require dotkernel/dot-authentication-service:^1.0\
dotkernel/dot-authentication-web:^1.0.1\
dotkernel/dot-controller:^1.0\
dotkernel/dot-controller-plugin-authentication:^1.0\
dotkernel/dot-controller-plugin-authorization:^1.0\
dotkernel/dot-controller-plugin-forms:^1.0\
dotkernel/dot-controller-plugin-flashmessenger:^1.0\
dotkernel/dot-controller-plugin-mail:^1.0\
dotkernel/dot-controller-plugin-session:^1.0\
dotkernel/dot-flashmessenger:^1.0\
dotkernel/dot-helpers:^1.0\
dotkernel/dot-mail:^1.0\
dotkernel/dot-mapper:^1.0\
dotkernel/dot-navigation:^1.0\
dotkernel/dot-rbac-guard:dev-migrate\
dotkernel/dot-session:^3.0\
dotkernel/dot-twigrenderer:^1.1\
dotkernel/dot-user:^1.0\
dotkernel/dot-rbac:^0.2.1\
zendframework/zend-expressive-helpers:^5.0\
zendframework/zend-expressive-twigrenderer:^2.0\
zendframework/zend-expressive-template:^2.0\
zendframework/zend-expressive:^3.0\
zendframework/zend-expressive-fastroute:^3.0\
zendframework/zend-expressive-tooling:^1.0\
zendframework/zend-expressive-router:^3.0\
zendframework/zend-stratigility:^3.0\
zendframework/zend-component-installer:^2.0

composer remove http-interop/http-middleware webimpress/http-middleware-compatibility
```
