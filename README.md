Поведение (behavior) для Yii2 позволяет удобно взаимодействовать с данными из json строке одной модели (родительской) через использование другой (обертки для данных).

# Установка
Выполните команду в теминале
```shell
> composer require "vitaly-alexandrovich/yii2-json-behavior"
```
или добавьте
```json
"vitaly-alexandrovich/yii2-json-behavior": "~1.0.1"
```
в секцию `require` файла `composer.json` в Вашем проекте

# Использование
```php
// Создаем модель для данных
class UserContacts extends yii\base\Model
{
    $phone;
    $email;
    $skype;
}

// В существующей ActiveRecord модели используем поведение JsonBehavior
class User extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            // Указываем колонку с данными и модель для них
            JsonBehavior::bind('contacts', Contacts::class),
        ];
    }
}
```

Теперь мы можем читать данные:
```php
    $user = User::findOne(1);

    $user->contacts->phone;
    $user->contacts->email;
    $user->contacts->skype;
```

Так и менять их:
```php
    $user = User::findOne(1);

    $user->contacts->phone = '+7 (111) 222-33-44';
    $user->contacts->email = 'user@examle.com';
    $user->contacts->skype = 'user';

    $user->save();
```