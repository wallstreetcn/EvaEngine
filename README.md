EvaEngine
=========

[![Build Status](https://travis-ci.org/EvaEngine/EvaEngine.svg?branch=master)](https://travis-ci.org/EvaEngine/EvaEngine)

[![Coverage Status](https://coveralls.io/repos/EvaEngine/EvaEngine/badge.png?branch=master)](https://coveralls.io/r/EvaEngine/EvaEngine?branch=master)

A development engine based on Phalcon Framework.

Thanks the icon from [Hrvoje Bielen](http://cargocollective.com/bielen)

### CORS
Add CORS support, you can define your own CORS support domains in the **config.local.php**:

```php
<?php
return [
  'cors' => [
    [
      'domain' => 'yourdomain1.com'
    ],
    [
      'domain' => 'yourdomain2.com'
    ]
  ]
];
```

You only need to add `'_cors_enabled' => true` to the specified route. For example:

**routes.frontend.php**
```php
<?php
return [
  '/posts' => [
    'module' => 'Information',
    'controller' => 'PostController',
    'action' => 'list',
    '_cors_enabled' => true // inform that this API is supported with CORS
  ]
];
?>
```
