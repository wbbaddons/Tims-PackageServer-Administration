{include file='header' pageTitle='wcf.acp.packageserver.permission.group.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.packageserver.permission.group.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link controller='PackageServerPackageGroupPermissionAdd'}{/link}{else}{link controller='PackageServerPackageGroupPermissionEdit' packageIdentifier=$packageIdentifier groupID=$group->groupID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.packageserver.permission{/lang}</legend>
			
			<dl{if $errorField == 'packageIdentifier'} class="formError"{/if}>
				<dt><label for="packageIdentifier">{lang}wcf.acp.packageserver.packageIdentifier{/lang}</label></dt>
				<dd>
					<input type="text" id="packageIdentifier" value="{$packageIdentifier}" name="packageIdentifier" required="required"{if $action != 'add'} disabled="disabled"{/if} class="medium" />
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
			
			<dl{if $errorField == 'groupIDs'} class="formError"{/if}>
				<dt><label for="groupIDs">{lang}wcf.user.group{/lang}</label></dt>
				<dd>
					{if $action == 'add'}
						<select name="groupIDs[]" id="groupIDs" multiple="multiple">
							{foreach from=$availableGroups item="group"}
								<option value="{$group->groupID}"{if $group->groupID|in_array:$groupIDs} selected="selected"{/if}>{lang}{$group->groupName}{/lang}</option>
							{/foreach}
						</select>
					{else}
						<input type="text" value="{lang}{$group->groupName}{/lang}" required="required" class="medium" disabled="" />
					{/if}
					
					{if $errorField == 'groupIDs'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.packageserver.groupIDs.error.{@$errorType}{/lang}
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
					<small>{lang}wcf.acp.packageserver.permissionString.description{/lang}</small>
				</dd>
			</dl>
		</fieldset>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{csrfToken}
	</div>
</form>

{include file='footer'}
