{include file='header' pageTitle='wcf.acp.packageserver.package.userpermission.add'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.packageserver.package.userpermission.add{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<form method="post" action="{link controller='PackageServerPackageUserPermissionAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.package.permission{/lang}</legend>

			<dl{if $errorField == 'packageIdentifier'} class="formError"{/if}>
				<dt><label for="packageIdentifier">{lang}wcf.acp.packageserver.packageIdentifier{/lang}</label></dt>
				<dd>
					<input type="text" id="packageIdentifier" value="{$packageIdentifier}" name="packageIdentifier" required="required" class="medium" />
					{if $errorField == 'packageIdentifier'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.packageserver.packageIdentifier.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
				
			<dl{if $errorField == 'permissionString'} class="formError"{/if}>
				<dt><label for="permissionString">{lang}wcf.acp.packageserver.permissionString{/lang}</label></dt>
				<dd>
					<input type="text" id="permissionString" value="{$permissionString}" name="permissionString" required="required" class="medium" />
					{if $errorField == 'permissionString'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.packageserver.permissionString.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
				
			<dl{if $errorField == 'username'} class="formError"{/if}>
				<dt><label for="username">{lang}wcf.user.username{/lang}</label></dt>
				<dd>
					<input type="text" id="usernames" name="usernames" value="{implode from=$usernames item=$username}{$username}{/implode}" required="required" class="medium" />
					{if $errorField == 'usernames'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.packageserver.usernames.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
