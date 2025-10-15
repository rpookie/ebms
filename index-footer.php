<footer>
  <span>eBMS @ 2025 <br><br> Gonzaga, Cagayan</span>
</footer>

<script>
// Additional initialization for index pages
document.addEventListener('DOMContentLoaded', function() {
    // Ensure main content has proper margin when sidebar is active
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    
    function adjustMainContent() {
        if (window.innerWidth > 768 && sidebar && mainContent) {
            if (sidebar.classList.contains("active")) {
                mainContent.style.marginLeft = "250px";
            } else {
                mainContent.style.marginLeft = "0";
            }
        } else {
            mainContent.style.marginLeft = "0";
        }
    }
    
    // Adjust on resize
    window.addEventListener('resize', adjustMainContent);
    
    // Initial adjustment
    adjustMainContent();
});
</script>