<div class="controlBox">
        <h2>Topic Management</h2>
        <p>Here you can edit topics. Except not, since it doesn't work.</p>
    </div>
    
		<form method="post" action="{geturl action='users'}?section=find_topic" id="form_find">
          <fieldset>
               <div class="row" id="">
                    <label for="form_find_user" class="grid_2">By topic ID:</label>
                    <input class="input_text" type="text" id="input_topicId" name="input_topicId" value="" />
               </div>

               <div class="row">
                    <label class="grid_2">&nbsp;</label>
                    <input type="submit" id="button_submit" value="Submit" />
               </div>
          </fieldset>
          </form>