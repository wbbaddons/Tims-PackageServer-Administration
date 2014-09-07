{include file='header' pageTitle='wcf.acp.packageserver.package.userpermission.add'}

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.packageserver.package.userpermission.add{/lang}</h1>
	</hgroup>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<div class="contentNavigation">

</div>

<form method="post" action="{link controller='PackageUserPermissionAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.permission{/lang}</legend>

			<dl{if $errorField == 'package'} class="formError"{/if}>
				<dt><label for="package">{lang}wcf.acp.packageserver.package{/lang}</label></dt>
				<dd>
					<input type="text" id="package" value="{$package}" name="package" required="required" class="medium" />
					{if $errorField == 'package'}
						<small class="innerError">
							{lang}wcf.global.form.error.empty{/lang}
						</small>
					{/if}
				</dd>
			</dl>
				
			<dl{if $errorField == 'permission'} class="formError"{/if}>
				<dt><label for="permission">{lang}wcf.acp.packageserver.permission{/lang}</label></dt>
				<dd>
					<input type="text" id="permission" value="{$permission}" name="permission" required="required" class="medium" />
					{if $errorField == 'permission'}
						<small class="innerError">
							{lang}wcf.global.form.error.empty{/lang}
						</small>
					{/if}
				</dd>
			</dl>
				
			<dl{if $errorField == 'username'} class="formError"{/if}>
				<dt><label for="username">{lang}wcf.acp.packageserver.username{/lang}</label></dt>
				<dd>
					<input type="text" id="username" name="username" value="{foreach from=$user item=$u}{$u->username}, {/foreach}" required="required" class="medium" />
					{if $errorField == 'username'}
						<small class="innerError">
							{lang}wcf.acp.packageserver.package.userpermission.error.username{/lang}
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