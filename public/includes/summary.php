<div class="bg-white main-report-area" style="">
  <h1 class="text-right  text-muted pr-15 pl-15 pt-15 fs-30"><i class="fa close-main-report-area fa-times"></i></h1>
  <!-- Institutions -->
  <?php if (session::exists("level")) : ?>
      <!-- start: province card -->
      <?php if (session::get('level') == 7 or session::get('level') == 1) : ?>
          <div class=" mt-0" id="province-card">
              <div class="panel panel">
                  <div class="panel-heading ">
                      <div class="row">
                          <div class="col-xs-12 text-right">
                              <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-group mr-10"></i> Intara</h4>
                              <table class="table">
                                  <tbody id="province-list"></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- end: province card -->

      <!-- start: district card -->
      <?php if (session::get('level') >= 6 or session::get('level') == 1) : ?>
          <div class=" mt-20 01" id="district-card">
              <div class="panel panel scroll-div">
                  <div class="panel-heading ">
                      <div class="row">
                          <div class="col-xs-12 text-right">
                              <h4 class="text-left mb-15 fw-700 d-flex j-space align-items-center">
                                  <span>Uturere tugize <span id="district-name"></span></span>
                              </h4>
                              <table class="table">
                                  <tbody id="district-list"></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- end: disctrict card -->

      <!-- start: sector card -->
      <?php if (session::get('level') >= 5 or session::get('level') == 1) : ?>
          <div class=" mt-20" id="sector-card">
              <div class="panel panel scroll-div">
                  <div class="panel-heading ">
                      <div class="row">
                          <div class="col-xs-12">
                              <h4 class="text-left mb-15 fw-700">
                                  <!-- <span><i class="menu-icon fw-400 fa fa-arrow-left mr-10"></i> Back </span> -->
                                  <span class="d-block fs-14 mt-10">Imirenge igize akarere ka <span id="sector-name"></span></span>
                              </h4>

                              <table class="table scrollTable">
                                  <tbody id="sector-list"></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- end: sector card -->

      <!-- start: cell card -->
      <?php if (session::get('level') >= 4 or session::get('level') == 1) : ?>
          <div class=" mt-20" id="cell-card">
              <div class="panel panel scroll-div">
                  <div class="panel-heading ">
                      <div class="row">
                          <div class="col-xs-12 text-right">
                              <h4 class="text-left mb-15 fw-700"> Utugari tugize umurenge wa <span id="cell-name"></span></h4>
                              <table class="table">
                                  <tbody id="cell-list"></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- end: cell card -->

      <!-- start: village card -->
      <?php if (session::get('level') >= 3 or session::get('level') == 1) : ?>
          <div class=" mt-20" id="village-card">
              <div class="panel panel scroll-div">
                  <div class="panel-heading ">
                      <div class="row">
                          <div class="col-xs-12 text-right">
                              <h4 class="text-left mb-15 fw-700"> Imidugudu igize akagari ka <span id="village-name"></span></h4>
                              <table class="table">
                                  <tbody id="village-list"></tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- end: village card -->
  <?php endif; ?>

  </center>
  <!-- <div> </div>           -->
</div>