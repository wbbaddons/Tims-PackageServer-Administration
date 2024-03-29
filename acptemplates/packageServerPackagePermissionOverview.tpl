{include file='header' pageTitle='wcf.acp.packageserver.package.permissionOverview'}

<script data-relocate="true">
	$('#permissionTableContainer').find('.jsDeleteButton').click(function(event) {
		event.preventDefault();
		
		WCF.System.Confirmation.show('{lang}wcf.acp.packageserver.permission.delete.confirmMessage{/lang}', function(action, target) {
			if (action === 'cancel') {
				return;
			}
			
			window.location = target.attr('href');
		}, $(this));
	});
</script>

{event name='javascriptInclude'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">
			{lang}wcf.acp.packageserver.package.permissionOverview{/lang} <span class="badge badgeInverse">{#$items}</span>
		</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li>
				<a href="{link controller='PackageServerPackageGeneralPermissionAdd'}{/link}" title="" class="button">
					<span class="icon icon16 fa-plus"></span>
					<span>{lang}wcf.acp.packageserver.permission.general.add{/lang}</span>
				</a>
			</li>
			
			<li>
				<a href="{link controller='PackageServerPackageUserPermissionAdd'}{/link}" title="" class="button">
					<span class="icon icon16 fa-plus"></span>
					<span>{lang}wcf.acp.packageserver.permission.user.add{/lang}</span>
				</a>
			</li>
			
			<li>
				<a href="{link controller='PackageServerPackageGroupPermissionAdd'}{/link}" title="" class="button">
					<span class="icon icon16 fa-plus"></span>
					<span>{lang}wcf.acp.packageserver.permission.group.add{/lang}</span>
				</a>
			</li>
		</ul>
		
		{event name='additonalNavigationLinks'}
	</nav>
</header>

{hascontent}
	<div class="paginationTop">
		{content}
			{assign var=encodedAction value=$action|rawurlencode}
			{pages print=true assign=pagesLinks controller="PackageServerPackagePermissionOverview" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
		{/content}
	</div>
{/hascontent}

{if $permissions|count}
	<div id="permissionTableContainer" class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle{if $sortField == 'packageIdentifier'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=packageIdentifier&sortOrder={if $sortField == 'packageIdentifier' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.packageIdentifier{/lang}</a></th>
					<th class="columnText{if $sortField == 'type'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=type&sortOrder={if $sortField == 'type' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.permission.type{/lang}</a></th>
					<th class="columnText{if $sortField == 'permissions'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=permissions&sortOrder={if $sortField == 'permissions' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.permission.value{/lang}</a></th>
					<th class="columnText{if $sortField == 'beneficiary'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=beneficiary&sortOrder={if $sortField == 'beneficiary' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.permission.beneficiary{/lang}</a></th>
					
					{event name='headColumns'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$permissions item=permission}
					<tr>
						<td class="columnIcon">
							{if $permission.type == "general"}
								<a href="{link controller='PackageServerPackageGeneralPermissionEdit' packageIdentifier=$permission.packageIdentifier}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
							{else if $permission.type == "user"}
								<a href="{link controller='PackageServerPackageUserPermissionEdit' packageIdentifier=$permission.packageIdentifier userID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
							{else if $permission.type == "group"}
								<a href="{link controller='PackageServerPackageGroupPermissionEdit' packageIdentifier=$permission.packageIdentifier groupID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
							{/if}
							
							<a href="{link controller='PackageServerDeletePermission' packageIdentifier=$permission.packageIdentifier type=$permission.type beneficiaryID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.delete{/lang}" class="jsDeleteButton jsTooltip"><span class="icon icon16 fa-times"></span></a>
						</td>
						<td class="columnTitle"><p>{$permission.packageIdentifier}</p></td>
						<td class="columnText "><p>{lang}wcf.acp.packageserver.permission.type.{$permission.type}{/lang}</p></td>
						<td class="columnText"><p>{$permission.permissionString}</p></td>
						<td class="columnText"><p>{if $permission.type == 'user'}{$permission.beneficiary}{else if $permission.type == 'group'}{lang}{$permission.beneficiary}{/lang}{else}–{/if}</p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

	<footer class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{@$pagesLinks}{/content}
			</div>
		{/hascontent}
		
		<nav class="contentFooterNavigation">
			<ul>
				<li>
					<a href="{link controller='PackageServerPackageGeneralPermissionAdd'}{/link}" title="" class="button">
						<span class="icon icon16 fa-plus"></span>
						<span>{lang}wcf.acp.packageserver.permission.general.add{/lang}</span>
					</a>
				</li>
				
				<li>
					<a href="{link controller='PackageServerPackageUserPermissionAdd'}{/link}" title="" class="button">
						<span class="icon icon16 fa-plus"></span>
						<span>{lang}wcf.acp.packageserver.permission.user.add{/lang}</span>
					</a>
				</li>
				
				<li>
					<a href="{link controller='PackageServerPackageGroupPermissionAdd'}{/link}" title="" class="button">
						<span class="icon icon16 fa-plus"></span>
						<span>{lang}wcf.acp.packageserver.permission.group.add{/lang}</span>
					</a>
				</li>
				
				{event name='additonalNavigationLinks'}
			</ul>
		</nav>
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
