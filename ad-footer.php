<footer>
  <span>eBMS Admin @ 2025 <br><br> Gonzaga, Cagayan</span>
</footer>

<script>
  // Toggle sidebar visibility
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    
    sidebar.classList.toggle("hidden");
    
    if (sidebar.classList.contains("hidden")) {
      mainContent.style.marginLeft = "0";
    } else {
      mainContent.style.marginLeft = "220px";
    }
  }
</script>
</body>
</html>