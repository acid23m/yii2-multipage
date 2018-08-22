Multi-Page
==========
Manage content on page that depends on URL query.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist acid23m/yii2-multipage "dev-master"
```

or add

```
"acid23m/yii2-multipage": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, do next:

Add module in `backend/config/main.php`.

```php
'module' => [
    'multipage' => [
        'class' => \multipage\Module::class,
        'layout' => '@backend/views/layouts/main.php'
    ]
]
```

Add menu links somewhere in admin panel to:

- `multipage/marker/index` - list of markers

- `multipage/marker/create` - create new marker

- `multipage/parameter/index` - list of get-parameters

- `multipage/parameter/create` - create new get-parameter

Register markers and rules in admin panel.

Replace markers in content for site front. There are three ways:

- Globally (not recommended). Search markers in every response.

```php
'components' => [
    'response' => [
        'on beforeSend' => function ($event) {
            /** @var \yii\web\Response $response */
            $response = $event->sender;
            if ($response->data !== null) {
                $response->data = \multipage\models\Process::replaceMarkers($response->data);
            }
        }
    ],
]
```

- As behavior for ActiveRecord.

```php
namespace frontend\models;

use common\models\post\PostRecord;
use multipage\behaviors\ReplaceMarkersBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class Post.
 *
 * @package frontend\models
 */
final class Post extends PostRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = [
            'replaceMarkers' => [
                'class' => ReplaceMarkersBehavior::class,
                'attribute' => 'description'
                //'attribute' => ['preview', 'description', 'text']
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

}
```

- Replace manually.

```php
namespace frontend\controllers;

use frontend\models\Post;
use yii\web\Controller;

/**
 * Site controller
 */
final class SiteController extends Controller
{
    /**
     * Displays post.
     * @return string
     */
    public function actionView($slug): string
    {
        $post = Post::find()
            ->select(['id', 'title', 'date', 'description'])
            ->show($slug)
            ->one();

        $post->description = \multipage\models\Process::replaceMarkers($post->description);

        return $this->render('view', compact('post'));
    }
}
```


Markers
-------

Markers - placeholders in the text that must be replaced.
Markers has *name* and *default text*.

- *name* is special code, e.g. `{{placeholder}}`.

- *default text* - `{{placeholder}}` will be replaced by *default text*
if no rule matches.


Parameters and Rules
--------------------

Markers are replaced by certain rules.
Replacement rules consists of:

- *marker*, e.g. `{{marker_str}}`.

- *get-parameter*, e.g. `utm_content`.

- *rule operator* - *equally* or *contains*.
The value of the *get-parameter* can exactly or partially coincide.

- *parameter value*.

- *replacement*. Markers will be replaced to this text
if rule matches. It can be HTML content.


Examples
--------

```
marker name = {{discount-1}}
default value = '' (empty string)
```

```
query parameter = utm_source
operator = equally
value = email
replacement = Congrats! You have personal 10% discount!
---
query parameter = utm_source
operator = equally
value = facebook
replacement = Congrats! You have personal 5% discount!
```

```
text on page = Just call Us to order this Item. {{discount-1}}
```

So for url `https://shop.com/item/123` filtered text on the front page will be

```
Just call Us to order this Item.
```

For url `https://shop.com/item/123?utm_source=email` filtered text on the front page will be

```
Just call Us to order this Item. Congrats! You have personal 10% discount!
```

And finally for url `https://shop.com/item/123?utm_source=facebook` filtered text on the front page will be

```
Just call Us to order this Item. Congrats! You have personal 5% discount!
```
