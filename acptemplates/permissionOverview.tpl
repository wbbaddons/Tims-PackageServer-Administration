{include file='header' pageTitle='Permission Overview'}

<header class="boxHeadline">
	<h1>{lang}Permission Overview{/lang}</h1>
</header>

<div class="contentNavigation">
	{hascontent}
	<nav>
		{content}
			{if $__wcf->session->getPermission('admin.packageServer.canAddPermissions')}
				<ul>
					<li>
						<a href="{link controller='PackageGeneralPermissionAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.addgeneral{/lang}</span>
						</a>
					</li>
				</ul>
						
				<ul>
					<li>
						<a href="{link controller='PackageUserPermissionAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.adduser{/lang}</span>
						</a>
					</li>
				</ul>
						
				<ul>
					<li>
						<a href="{link controller='PackageGroupPermissionAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.addgroup{/lang}</span>
						</a>
					</li>
				</ul>
			{/if}
			
			{event name='additonalNavigationLinks'}
		{/content}
	</nav>
	{/hascontent}
</div>

{hascontent}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.packageserver.permission.list{/lang}</h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle">{lang}wcf.acp.packageserver.permission.package{/lang}</th>
					<th class="columnText">{lang}wcf.acp.packageserver.permission.type{/lang}</th>
					
					{event name='headColumns'}
				</tr>
			</thead>

			<tbody>
				{content}
				{foreach from=$items item=item}
					<tr>
						<td class="columnTitle"><p>{$item.packageIdentifier}</p></td>
						<td class="columnText "><p>{lang}wcf.acp.packageserver.permission.type.{$item.type}{/lang}</p></td>

						{event name='columns'}
					</tr>
				{/foreach}
				{/content}
			</tbody>
		</table>

	</div>
{hascontentelse}
	<p class="info">{lang}wcf.acp.packageserver.permission.noresults{/lang}</p>
{/hascontent}

<div class="contentNavigation">
	{hascontent}
	<nav>
		{content}
			{if $__wcf->session->getPermission('admin.packageServer.canAddPermissions')}
				<ul>
					<li>
						<a href="{link controller='PackageGeneralPermissionAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.addgeneral{/lang}</span>
						</a>
					</li>
				</ul>
						
				<ul>
					<li>
						<a href="{link controller='PackageGeneralUserAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.adduser{/lang}</span>
						</a>
					</li>
				</ul>
						
				<ul>
					<li>
						<a href="{link controller='PackageGeneralGroupAdd'}{/link}" title="" class="button">
							<span class="icon icon16 icon-plus"></span>
							<span>{lang}wcf.acp.packageserver.permission.addgroup{/lang}</span>
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
