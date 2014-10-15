{include file='header' pageTitle='wcf.acp.packageserver.package.permissionOverview'}

<script data-relocate="true">
	$('#permissionTableContainer').find('.jsDeleteButton').click(function(event) {
		event.preventDefault();
		
		WCF.System.Confirmation.show('{lang}wcf.acp.packageserver.package.delete.confirmMessage{/lang}', function(action, target) {
			if (action === 'cancel') {
				return;
			}
			
			window.location = target.attr('href');
		}, $(this));
	});
</script>

{event name='javascriptInclude'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.packageserver.package.permissionOverview{/lang}</h1>
</header>

{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="PackageServerPackagePermissionOverview" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	{hascontent}
		<nav>
			{content}
				{if $__wcf->session->getPermission('admin.packageServer.canManagePackages')}
					<ul>
						<li>
							<a href="{link controller='PackageServerPackageGeneralPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.generalpermission.add{/lang}</span>
							</a>
						</li>
						
						<li>
							<a href="{link controller='PackageServerPackageUserPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.userpermission.add{/lang}</span>
							</a>
						</li>
						
						<li>
							<a href="{link controller='PackageServerPackageGroupPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.grouppermission.add{/lang}</span>
							</a>
						</li>
					</ul>
				{/if}
				
				{event name='additonalNavigationLinks'}
			{/content}
		</nav>
	{/hascontent}
</div>

{if $permissions|count}
	<div id="permissionTableContainer" class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.packageserver.package.permission.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle{if $sortField == 'packageIdentifier'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=packageIdentifier&sortOrder={if $sortField == 'packageIdentifier' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.package.identifier{/lang}</a></th>
					<th class="columnText{if $sortField == 'type'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=type&sortOrder={if $sortField == 'type' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.package.permission.type{/lang}</a></th>
					<th class="columnText{if $sortField == 'permissions'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=permissions&sortOrder={if $sortField == 'permissions' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.package.permission.value{/lang}</a></th>
					<th class="columnText{if $sortField == 'beneficiary'} active {@$sortOrder}{/if}"><a href="{link controller='PackageServerPackagePermissionOverview'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=beneficiary&sortOrder={if $sortField == 'beneficiary' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.packageserver.package.permission.beneficiary{/lang}</a></th>
					
					{event name='headColumns'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$permissions item=permission}
					<tr>
						<td class="columnIcon">
							{if $permission.type == "general"}
								<a href="{link controller='PackageServerPackageGeneralPermissionEdit' packageIdentifier=$permission.packageIdentifier}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							{else if $permission.type == "user"}
								<a href="{link controller='PackageServerPackageUserPermissionEdit' packageIdentifier=$permission.packageIdentifier userID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							{else if $permission.type == "group"}
								<a href="{link controller='PackageServerPackageGroupPermissionEdit' packageIdentifier=$permission.packageIdentifier groupID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							{/if}
							
							<a href="{link controller='PackageServerDeletePermission' packageIdentifier=$permission.packageIdentifier type=$permission.type beneficiaryID=$permission.beneficiaryID}{/link}" title="{lang}wcf.global.button.delete{/lang}" class="jsDeleteButton jsTooltip"><span class="icon icon16 icon-remove"></span></a>
						</td>
						<td class="columnTitle"><p>{$permission.packageIdentifier}</p></td>
						<td class="columnText "><p>{lang}wcf.acp.packageserver.package.permission.type.{$permission.type}{/lang}</p></td>
						<td class="columnText"><p>{$permission.permissionString}</p></td>
						<td class="columnText"><p>{if $permission.type == 'user'}{$permission.beneficiary}{else if $permission.type == 'group'}{lang}{$permission.beneficiary}{/lang}{else}–{/if}</p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
		
	</div>
{else}
	<p class="info">{lang}wcf.acp.packageserver.package.permission.noresults{/lang}</p>
{/if}

<div class="contentNavigation">
	{@$pagesLinks}
	
	{hascontent}
		<nav>
			{content}
				{if $__wcf->session->getPermission('admin.packageServer.canManagePackages')}
					<ul>
						<li>
							<a href="{link controller='PackageServerPackageGeneralPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.generalpermission.add{/lang}</span>
							</a>
						</li>
						
						<li>
							<a href="{link controller='PackageServerPackageUserPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.userpermission.add{/lang}</span>
							</a>
						</li>
						
						<li>
							<a href="{link controller='PackageServerPackageGroupPermissionAdd'}{/link}" title="" class="button">
								<span class="icon icon16 icon-plus"></span>
								<span>{lang}wcf.acp.packageserver.package.grouppermission.add{/lang}</span>
							</a>
						</li>
					</ul>
				{/if}
				
				{event name='additonalNavigationLinks'}
			{/content}
		</nav>
	{/hascontent}
</div>

{include file='footer'}
