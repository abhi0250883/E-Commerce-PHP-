<?php

require('Dbconnection.php');

if (isset($_POST['csb'])) {
  $cname    = $_POST['cname'];
  $audience = $_POST['audience'];
  $cstatus  = isset($_POST['cstatus']) ? 1 : 0;

  // Arrays ko string me convert karna
  $subcat   = isset($_POST['subcat']) ? implode(",", $_POST['subcat']) : "";
  $seasons  = isset($_POST['seasons']) ? implode(",", $_POST['seasons']) : "";
  $occasions = isset($_POST['occasions']) ? implode(",", $_POST['occasions']) : "";

  $csql = "INSERT INTO `catagory`
          (`cname`,`subcat`,`audience`,`seasons`,`occasions`,`cstatus`) 
          VALUES 
          ('$cname','$subcat','$audience','$seasons','$occasions',$cstatus)";

  $cresult = mysqli_query($con, $csql);

  if ($cresult) {
    echo "✅ Category inserted successfully";
  } else {
    echo "❌ Error: " . mysqli_error($con);
  }
}
?>





<div class="container my-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <h1 class="h5 mb-0">Create Clothing Category</h1>
        </div>
        <div class="card-body">
          <form id="categoryForm" action="" method="POST" enctype="multipart/form-data" novalidate>
            <div class="row g-3">
              <!-- Name -->
              <!-- <div class="col-12">
                <label for="name" class="form-label">Category Name *</label>
                <input id="name" name="cname" type="text" class="form-control" placeholder="e.g., Ethnic Wear" required />
                <div id="nameErr" class="invalid-feedback">Please enter a category name.</div>
              </div> -->

              <!-- Subcategories + Audience -->
              <!-- <div class="col-md-6">
                <label for="subcatInput" class="form-label">Subcategory*</label>
                <input id="subcatInput" name="subcat" type="text" class="form-control" placeholder="e.g., Sarees (press Enter to add)" />
                <div class="form-text">Type and press Enter (or comma) to add multiple.</div>
                <ul id="subcatList" class="chips d-flex flex-wrap gap-2 mt-2" aria-live="polite"></ul>
              </div> -->

              <div class="col-md-6">
                <label for="audience" class="form-label">Audience*</label>
                <select id="audience" name="audience" class="form-select" required>
                  <option value="">Unspecified</option>
                  <option value="women">Women</option>
                  <option value="men">Men</option>
                  <option value="girls">Girls</option>
                  <option value="boys">Boys</option>
                  <option value="unisex">Unisex</option>
                </select>
              </div>

              <!-- Seasons -->
              <!-- <div class="col-12">
                <label class="form-label d-block">Season</label>
                <div class="d-flex flex-wrap gap-2">
                  <input type="checkbox" class="btn-check" id="season-spring" name="seasons[]" value="Spring" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="season-spring">Spring</label>

                  <input type="checkbox" class="btn-check" id="season-summer" name="seasons[]" value="Summer" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="season-summer">Summer</label>

                  <input type="checkbox" class="btn-check" id="season-autumn" name="seasons[]" value="Autumn" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="season-autumn">Autumn</label>

                  <input type="checkbox" class="btn-check" id="season-winter" name="seasons[]" value="Winter" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="season-winter">Winter</label>
                </div>
              </div> -->

              <!-- Occasions -->
              <!-- <div class="col-12">
                <label class="form-label d-block">Occasion</label>
                <div class="d-flex flex-wrap gap-2">
                  <input type="checkbox" class="btn-check" id="occ-marriage" name="occasions[]" value="Marriage" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-marriage">Marriage</label>

                  <input type="checkbox" class="btn-check" id="occ-family" name="occasions[]" value="Family Function" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-family">Family Function</label>

                  <input type="checkbox" class="btn-check" id="occ-casual" name="occasions[]" value="Casual Wear" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-casual">Casual Wear</label>

                  <input type="checkbox" class="btn-check" id="occ-Office" name="occasions[]" value="Office Wear" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-Office">Office Wear</label>

                  <input type="checkbox" class="btn-check" id="occ-Travel" name="occasions[]" value="Travel / Vacation" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-Travel">Travel / Vacation</label>

                  <input type="checkbox" class="btn-check" id="occ-festive" name="occasions[]" value="Festive / Traditional Occasions" autocomplete="off">
                  <label class="btn btn-outline-primary rounded-pill" for="occ-festive">Festive / Traditional Occasions</label>
                </div>
              </div> -->

              <!-- Description -->
              <!-- <div class="col-12">
                <label for="description" class="form-label">Description (optional)</label>
                <textarea id="description" name="description" class="form-control" placeholder="Short blurb for the category page..." rows="4"></textarea>
              </div> -->

              <!-- Active + Display Order -->
              <!-- <div class="col-md-6">
                <label class="form-label d-block">Status</label>
                <div class="form-check form-switch">
                  <input id="is_active" name="cstatus" class="form-check-input" type="checkbox" value="1" checked />
                  <label class="form-check-label" for="is_active">Active</label>
                </div>
              </div> -->
              <button type="submit" name="csb" class="btn btn-primary w-100">Save Catagory</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
  (function() {
    const form = document.getElementById('categoryForm');
    const nameEl = document.getElementById('name');
    const nameErr = document.getElementById('nameErr');
    const subcatInput = document.getElementById('subcatInput');
    const subcatList = document.getElementById('subcatList');

    const subcats = new Map();

    const escapeHTML = (s) => s.replace(/[&<>"']/g, c => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    } [c]));

    const renderSubcatChip = (label) => {
      const trimmed = label.trim();
      if (!trimmed) return;
      const key = trimmed.toLowerCase();
      if (subcats.has(key)) return;

      subcats.set(key, trimmed);

      const li = document.createElement('li');
      li.className = 'badge text-bg-light border rounded-pill px-2 py-2';
      li.innerHTML = `
      <span class="me-1">${escapeHTML(trimmed)}</span>
      <button type="button" class="btn btn-sm btn-link link-danger p-0 lh-1 remove">
        <span aria-hidden="true">&times;</span>
      </button>
      <input type="hidden" name="subcat[]" value="${escapeHTML(trimmed)}">
    `;
      li.querySelector('.remove').addEventListener('click', () => {
        subcats.delete(key);
        li.remove();
      });
      subcatList.appendChild(li);
    };

    const addSubcategoriesFromInput = () => {
      const raw = subcatInput.value;
      if (!raw.trim()) return;
      raw.split(',').map(s => s.trim()).filter(Boolean).forEach(renderSubcatChip);
      subcatInput.value = '';
    };

    // --- Events ---
    subcatInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        addSubcategoriesFromInput();
      }
    });
    subcatInput.addEventListener('blur', addSubcategoriesFromInput);
    subcatInput.addEventListener('input', (e) => {
      if (e.data === ',') addSubcategoriesFromInput();
    });

    form.addEventListener('submit', (e) => {
      if (!nameEl.value.trim()) {
        e.preventDefault();
        nameEl.classList.add('is-invalid');
        nameErr.style.display = 'block';
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      } else {
        nameEl.classList.remove('is-invalid');
        nameErr.style.display = 'none';
      }
    });
  })();
</script>


<style>
  /* Minimal extras just for the chips list */
  ul.chips {
    list-style: none;
    padding-left: 0;
  }

  ul.chips li {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
  }

  .img-preview {
    display: none;
    max-width: 320px;
    height: auto;
  }

  pre#preview {
    max-height: 60vh;
    overflow: auto;
  }
</style>