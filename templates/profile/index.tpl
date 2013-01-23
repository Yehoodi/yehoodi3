{include file='header.tpl' section='profile' maps=false}

{include file='profile/profile.tpl'}

{include file='footer.tpl'}
<script type="text/javascript" src="/js/profile/Profile.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new Profile();
</script>
