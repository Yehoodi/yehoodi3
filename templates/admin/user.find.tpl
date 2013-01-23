    <div class="controlBox">
        <h2>User Management</h2>
        <p>Here you can change your users' information and certain options.</p>
    </div>

    <form method="post" action="{geturl action='users'}?section=find_user" id="form_find">
        <fieldset>
            <div class="row" id="">
                <label for="form_find_user" class="grid_2">By user name:</label>
                <input class="input_text" type="text" id="input_userName" name="input_userName" value="" />
            </div>        
            <div class="row" id="">
                <label for="form_find_user)id" class="grid_2">By User ID:</label>
                <input class="input_text" type="text" id="input_userId" name="input_userId" value="" />
            </div>        
            <div class="row" id="">
                <label for="form_find_user" class="grid_2">Pick from list:</label>
                <select size="20" name="select_userName" class="input_text">
                    {foreach from=$userName item=usr}
                    <option value="{$usr.user_id}">{$usr.user_name}</option>
                    {/foreach}
                </select>
            </div>        
            <div class="row">
                <label class="grid_2">&nbsp;</label>
                <input type="submit" id="button_submit" value="Submit" />
            </div>
        </fieldset>
    </form>	
