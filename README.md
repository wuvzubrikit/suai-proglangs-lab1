# Отчет по лабораторной работе №1: "HTTP аутентификация"

## Цель работы:
Спроектировать и разработать систему авторизации пользователей на протоколе HTTP.

## Ход работы
### Пользовательский интерфейс
![рис. 1](https://github.com/wuvzubrikit/suai-proglangs-lab1/blob/main/user_interface.jpg)

При входе на сайт пользователь попадает на страницу ***/index.php***, которая является заглавной страницей сайта. Однако в силу ее временного отсутсвия происходит мгновенный редирект на страницу ***/login.php***. На ней представлена простая форма регистрации с полями ввода имени пользователя и пароля и кнопкой отправки данных, а также ссылки для перехода на страницу с регистрацией ***/registration.php*** и страницу восстановления пароля. ***/forgot_password.php***.
###### 1. **login.php** 
Основное действие на данной странице - это вход в профиль по имени пользователя и паролю. В случае успешной ауетентификации пользователь авторизируется на собственную страничку ***/profile.php***. Если же пользователь ввел неправильные данные, то под полями ввода появляется блок с сообщением о соответствующей ошибке ("Wrong username" / "Wrong password").
###### 2. **registartion.php** 
На странице регистрации пользователю предоставляется возможным ввести свои данные: имя пользователя, электронный почтовый ящик, а также пароль и его подтверждение. Далее данные проходят базовую валидацию, и если проверка выполняется успешно, то пользователь перенаправляется на страницу входа, в ином случае под полями ввода появляется блок с сообщением о соответствующей ошибке.
###### 3. **forgot_password.php**
На данной странице происходит сброс пароля пользователя. По полученным значениям электронной почты и имени пользователя ищется соответствующий пользователь. Если такой пользователь найден, то на его почту высылается временная ссылка на страницу сброса пароля ***/reset_password.php?token=%reset_token***, если же пользователь не найден, то под полями ввода появляется блок с сообщением о соответствующей ошибке. 
> Для данной лабораторной работы под "почтой" подразумевается соответствующая директория на сервере (/emails/$email), а письмом является файл, создаваемый в этой директории и имеющий название в виде текущей даты со временем (пример - emails\admin@localhost\2022-10-15-113538.txt).

###### 4. **reset_password.php**
На этой странице пользователь может создать новый пароль. Если пароль проходит валидацию, то юзер перенаправляется на страницу входа с сообщением об успешной смене пароля. Доступ на данную странцу предоставляется только с временным токеном восстановления на 10 минут, поэтому по истечении этого времени пользователя перешлет на страницу входа с сообщением об истчении срока действия ссылки.
###### 4. **profile.php**
После успешной авторизации пользователь оказывается в своем профиле. На этой странице имеется заголовок принадлежности страницы пользователю, а также две ссылки: на скрипт с выходом из профиля (*/include/dologout.php*) и страницу с изменением пароля ***/change_password.php***.
###### 5. **change_password.php**
На данной странице  пользователь может изменить пароль. Для этого необходимо ввести текущий пароль, новый пароль и подтвердить его. Если все прошло успешно, то пользователь автомаитчески разлогинивается и перебрасывается на страницу входа с сообщением о том, что требуется переавторизация. Иначе под полями ввода появляется блок с сообщением о соответствующей ошибке.

### API сервера
API сервера представлен файлами, находящимися в  директории "/includes/". 
###### 1. **dologin.php** 
[6-11]: Скрипт выполняет валидацию POST-параметра *username*. 
> [6-11] — строки кода в описываемом файле

Обработка ошибок происходит за счет создания значения в суперглобальном массиве `$_SESSION` с ключом `error-message`/`warning-message`/`success-message` и перенаправлением на страницу-рефферер.

[14-27]: Проверка на наличие пользователя в базе данных. Обращение к MySQL осуществляется за счет PHP Data Objects, так как этот класс предоставляет удобные методы работы с различными СУБД, а также преобработку запросов, что позволяет избежать SQL-инъекций.
Данные пользователя заносятся в `$_SESSION['user']`.

[30-37]: Проверка пароля путем сравнения хэшей. В качестве алгоритма хэширования был выбран PBKDF2, основывающийся на SHA256-HMAC с 1000 итерациями.

[40-42]: Проверка срока действия пароля. В случае, если пароль используется больше 6-ти месяцев, то в профиле всплывает соответствующее предупреждение с рекомендацией сменить пароль. После смены пароля сообщение исчезает.

[45-48]: Формирование сессионной cookie. Так, `$_SESSION['user_startup_time']` хранит в себе время создания куки, а персональный токен пользователя `$_SESSION['user_token']` образуется на основе user_id и username. Сама куки является дайджестом SHA256 от этих двух параметров и устанавливается на 1 час. Данный метод формирование куки позволяет минимизировать ее фабрикацию и гарантирует конфиденциальность сессии.

###### 2. **dosignup.php** 
[6-32]: Скрипт выполняет валидацию предоставляемых для регистрации данных.
[35-46]: Подключение к БД и проверка на существование пользователя.
[49-53]: Фильтрация входных параметров и внесение их в словарь `$user`.
[56]: Создание псевдо-почтового ящика.
[58-70]: Генерация соли, срока истечения действия пароля и внесение данных в БД.

###### 3. **dologout.php** 
[4-6]: Очистка сессионных данных пользователя, а также куки.

###### 4. **dochangepass.php** 
Данный сценарий выполняет схожие действия, что и dosignup.php, а также убирает сообщение о необходимости смене пароля (если имеется) [50-53] и разлогинивает пользователя из сессии.

###### 5. **doresetprep.php** 
[21-35]: Проверка на существование пользователя по паре ключей {username; email}.
[38-40]: Создание токена сброса пароля, действительного 6 минут.
[42-56]: Создание письма с ссылкой на страницу сброса пароля. Если почтовый ящик не найден, он создается снова.

###### 6. **doresetpass.php** 
[6-18]: Валидация ввода.
[20-36]: Обновление базы данных с новым паролем и сопутствующими параметрами. 

###### 7. **utils.php** 
В данном файле представлены вспомогательные функции, вставляемые в HTML код.
[3-16]: `CookieIsResourceForbidden()` - функция, проверяющая наличие сессионной куки  при ее отсутствии запрщещающая просмотр страницы, возвращая код 403. 
[18-31]: `CookieIsUnauthorized()` - функция, проверяющая наличие сессионной куки  при ее отсутствии запрщещающая просмотр страницы, возвращая код 401.
[33-45]: `CookieIsInvalid()` - функция, выполняющая проверку текущей куки и в случае несоответствия выполняющая лог-аут из сессии.
[47-64]: `PrintSessionMessagesU()` - процедура, выводящаяя сообщения в тэг <p> для неавторизованного пользователя.
[66-87]: `PrintSessionMessagesA()` - процедура, выводящаяя сообщения в тэг <p> для авторизованного пользователя.

### Структура базы данных
#### user_data
| id | username | password  | salt | password_expiration | email|
|-|-|-|-|-|-|
||||||||||||
В этой таблице:
-- **id** - идентификатор пользователя (int), ключевое значение с автоматическим инкрементом;
-- **username** - имя пользователя (varchar(32));
-- **password** - хэш пароля (varchar(32));
-- **salt** - соль пароля (varchar(32));
-- **password_expiration** - дата срока истечения действия пароля (datetime);
-- **email** - почтовый ящик пользователя (varchar(32)).

### Вывод
По итогу лабораторной работы была спроектирована система авторизации пользователя с наличием примитивного профиля на протоколе HTTP с использованием стэка вэб-разработки XAMP.