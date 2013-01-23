               <select name="sort" id="select_sort_types">
                    <option {if $order == "date"}selected{/if} value="date">Most recently submitted</option>
                    <option {if $order == "popular"}selected{/if} value="popular">Most votes</option>
                    <option {if $order == "comment"}selected{/if} value="comment">Most commented</option>
                    <option {if $order == "views"}selected{/if} value="views">Most viewed</option>
                    <option {if $order == "activity"}selected{/if} value="activity">Activity (last 30 days)</option>
               </select>
