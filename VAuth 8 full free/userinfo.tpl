{userifo-style}

<div class="pheading">
	<h2 class="lcol">Пользователь: <span>{usertitle}</span></h2>
	<div class="ratebox"><div class="rate">{rate}</div><span>Рейтинг:</span></div>
	<div class="clr"></div>
</div>
<div class="basecont"><div class="dpad">
	<div class="userinfo">
		<div class="lcol">
			<div class="avatar"><img src="{foto}" alt=""/></div>
			<ul class="reset">
				<li>{email}</li>
				[not-group=5]
				<li>{pm}</li>
				[/not-group]
			</ul>
		</div>
		
		<div class="rcol">
			<ul>
				<li><span class="grey">Полное имя:</span> <b>{fullname}</b></li>
				[vauth-bdate]<li><span class="grey">Дата рождения:</span> {bdate}</li>[/vauth-bdate]
				[vauth-sex]<li><span class="grey">Пол:</span> {sex}</li>[/vauth-sex]
				[vauth-mobile_phone]<li><span class="grey">Телефон:</span> {mobile_phone}</li>[/vauth-mobile_phone]
				<li><span class="grey">Группа:</span> {status} [time_limit]&nbsp;В группе до: {time_limit}[/time_limit]</li>
				[vauth-friends]<li><span class="grey">Друзья:</span> {friends}</li>[/vauth-friends]
				[vauth]<br/><li><span class="grey">{accounts} [not-logged]<a class="account_link big" href="/index.php?do=account_connect">&#8594; управление</a>[/not-logged]</span> </li>
				<br/>
				[/vauth]
			</ul>
			<ul class="ussep" style="clear:left important!;">
				<li><span class="grey">Количество публикаций:</span> <b>{news-num}</b> [{news}][rss]<img src="{THEME}/images/rss.png" alt="rss" style="vertical-align: middle; margin-left: 5px;" />[/rss]</li>
				<li><span class="grey">Количество комментариев:</span> <b>{comm-num}</b> [{comments}]</li>
				<li><span class="grey">Дата регистрации:</span> {registration}</li>
				<li><span class="grey">Последнее посещение:</span> {lastdate}</li>
			</ul>
			<ul class="ussep">
				<li><span class="grey">Место жительства:</span> {land}</li>
				<li><span class="grey">Немного о себе:</span> {info}</li>
			</ul>
			<span class="small">{edituser}</span>
		</div>
		<div class="clr"></div>
	</div>
</div></div>
[not-logged]
<div id="options" style="display:none;">
	<br /><br />

	<div class="pheading"><h2>Редактирование профиля</h2></div>
	<div class="baseform">
		<table class="tableform">
			<tr>
				<td class="label">Ваше Имя:</td>
				<td><input type="text" name="fullname" value="{fullname}" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label">Ваш E-Mail:</td>
				<td><input type="text" name="email" value="{editmail}" class="f_input" /><br />
				<div class="checkbox">{hidemail}</div>
				<div class="checkbox"><input type="checkbox" id="subscribe" name="subscribe" value="1" /> <label for="subscribe">Отписаться от подписанных новостей</label></div></td>
			</tr>
			<tr>
				<td class="label">Место жительства:</td>
				<td><input type="text" name="land" value="{land}" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label">Список игнорируемых пользователей:</td>
				<td>{ignore-list}</td>
			</tr>
			<tr>
				<td class="label">Номер ICQ:</td>
				<td><input type="text" name="icq" value="{icq}" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label">Старый пароль:</td>
				<td><input type="password" name="altpass" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label">Новый пароль:</td>
				<td><input type="password" name="password1" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label">Повторите:</td>
				<td><input type="password" name="password2" class="f_input" /></td>
			</tr>
			<tr>
				<td class="label" valign="top">Блокировка по IP:<br />Ваш IP: {ip}</td>
				<td>
				<div><textarea name="allowed_ip" style="width:98%;" rows="5" class="f_textarea">{allowed-ip}</textarea></div>
				<div>
					<span class="small" style="color:red;">
					* Внимание! Будьте бдительны при изменении данной настройки.
					Доступ к Вашему аккаунту будет доступен только с того IP-адреса или подсети, который Вы укажете.
					Вы можете указать несколько IP адресов, по одному адресу на каждую строчку.
					<br />
					Пример: 192.48.25.71 или 129.42.*.*</span>
				</div>
				</td>
			</tr>
			<tr>
				<td class="label">Аватар:</td>
				<td>
				<input type="file" name="image" class="f_input" /><br />
				<div class="checkbox"><input type="checkbox" name="del_foto" id="del_foto" value="yes" /> <label for="del_foto">Удалить фотографию</label></div>
				</td>
			</tr>
			<tr>
				<td class="label">О себе:</td>
				<td><textarea name="info" style="width:98%;" rows="5" class="f_textarea">{editinfo}</textarea></td>
			</tr>
			<tr>
				<td class="label">Подпись:</td>
				<td><textarea name="signature" style="width:98%;" rows="5" class="f_textarea">{editsignature}</textarea></td>
			</tr>
			{xfields}
		</table>
		<div class="fieldsubmit">
			<input class="fbutton" type="submit" name="submit" value="Отправить" />
			<input name="submit" type="hidden" id="submit" value="submit" />
		</div>
	</div>
</div>
[/not-logged]