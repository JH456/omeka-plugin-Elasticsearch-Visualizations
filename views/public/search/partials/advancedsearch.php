
<!--<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">-->

<script>
    
    function modalOpen() {
        document.getElementById("advancedSearchModal").style.display = "block";
    }
    
    function modalClose() {
        document.getElementById("advancedSearchModal").style.display = "none";
    }
    
</script>

<div id="advancedSearchModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div >
            
            <span onclick="modalClose()" class="w3-button w3-display-topright">&times;</span>
            
            <div class="w3-row">
            
                <!-- Search pane -->
                
                <div class="w3-half" style="background-color: #eeb211; height:340px">
                    <div class="w3-container"> <h2 style="color: #ffffff;">Advanced Search</h2> </div>
                    
                    <div class="w3-container">
                        
                        <form id="elasticsearch-search-form">
                            <input type="text"
                                   class="w3-input"
                                   placeholder="Search by title, text, tags, etc." 
                                   title="<?php echo __('Search keywords') ?>"
                                   name="q"
                                   value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
                            <input class="w3-button" type="submit" value="Search"/>
                        </form>
                        
                    </div>
                    
                </div>
                
                <!-- Search help pane -->
                
                <div class="w3-half w3-pale-yellow">
                    <div class="w3-container w3-dark-gray"> <h2 style="color: #ffffff;">Help</h2> </div>
                    
                    <div class="w3-row w3-container">
                        <div class="w3-half"> <p>Search by <b>fields:</b></p> </div>
                        <div class="w3-half">
                            <p><code>title:"Inhabited Spaces"</code><br>
                               <code>tags:forts</code><br>
                        </div>
                    </div>
                    
                    <div class="w3-row w3-container">
                        <div class="w3-half"> <p>Search with boolean operands or wildcards:</p> </div>
                        <div class="w3-half">
                            <p><code>paris AND fortifications</code><br>
                               <code>title:paris AND tags:western</code><br>
                               <code>184?s OR 185?s</code><br></p>
                        </div>
                    </div>
                    
                    <div class="w3-row w3-container">
                        <div class="w3-half"> <p><b>Boost</b> specific terms:</p> </div>
                        <div class="w3-half">
                            <p><code>paris^2 western</code><br>
                               <code>apple banana^5</code><br></p>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
    </div>
</div>