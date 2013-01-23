
<div class="controlBox">
        <h2>Manual SVN Pushes</h2>
        <p>For ADMINS only. Don't push these buttons if you don't know what you are doing.</p>
    </div>


		<form method="post" action="{geturl action='site'}?section=svn" id="form_svn">
          <fieldset>
               <div class="row" id="">
                    <label for="form_svn_up" class="grid_3">Choose a site to svn update:</label>
                    <select name="svn_update" id="form_svn_up">
                         <option value="alba">Dev (Alba)</option>
                         <option value="calloway">Stage (Calloway) - Disabled</option>
                    </select>
               </div>

                <div class="row" style="margin-bottom: 2em;">
                    <label class="grid_3">&nbsp;</label>
                    <input type="submit" id="button_submit" value="Submit" />
               </div>

               <div id="row">
                    <label for="form_svn_output" class="grid_3">SVN output:</label>
                    <textarea class="input_text" readonly="readonly" style="width: 500px;">{$svnOutput}</textarea>
               </div>		

          </fieldset>
        </form>