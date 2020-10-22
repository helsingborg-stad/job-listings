import setQuery from 'set-query-string';

class JobListings {
  constructor() {
    this.handleEvents();
    this.renderApplicationIframe();
  }

  handleEvents = () => {
    const { jobId } = jobListings;
    const applyButtons = document.getElementsByClassName('js-job-listings-apply');
    const loginButton = document.querySelector('[js-trigger-btn-id]');

    for (let i = 0; i < applyButtons.length; i++) {
      const button = applyButtons.item(i);
      button.addEventListener('click', e => {
        setQuery(
          {
            rmpage: 'apply',
            rmjob: jobId,
          },
          { pushState: true, clear: true }
        );

        this.renderApplicationIframe();
      });
    }

    loginButton.addEventListener('click', e => {
      setQuery(
        {
          rmpage: 'auth',
        },
        { pushState: true, clear: true }
      );
      this.renderApplicationIframe();
    });
  };


  renderApplicationIframe = () => {
    // from db
    let iFrameUrl = 'https://web103.reachmee.com/ext/';
    const validator = '9ec24d855e37a090a5e232077f53c593';
    const iid = 'I017';
    const customer = '1118';
    const site = '9';
    const iHelperUrl = 'https://helsingborg.se/arbete/lediga-jobb-i-helsingborgs-stad/';
    const height = '4000';
    const langDef = 'SE';
    const title = 'ReachMee Rekrytera extern webbplats';
    let ref = encodeURIComponent(document.referrer);

    // form url
    let jobId = '';
    let destPage = '';
    let langId = '';
    let rmproj = '';
    let rmihelper;

    //
    function getQueryVariable(variable) {
      if (variable.toLowerCase() == 'rmurl') {
        var query = decodeURIComponent(window.location.search.substring(1));
      } else {
        var query = window.location.search.substring(1);
      }
      const vars = query.split('&');
      for (let i = 0; i < vars.length; i++) {
        const pair = vars[i].split('=');
        if (pair[0] == variable) {
          if (pair.length > 2) return pair[2];
          return pair[1];
        }
      }
    }

    iFrameUrl += iid + '/' + customer + '/';

    destPage = getQueryVariable('rmpage');
    jobId = getQueryVariable('rmjob');
    langId = getQueryVariable('rmlang');
    rmproj = getQueryVariable('rmproj');
    rmihelper = getQueryVariable('rmihelper');

    if (typeof destPage === 'undefined' || destPage == '') {
      destPage = 'main?';
    } else {
      destPage += '?';
    }
    if (destPage == 'job?' || destPage == 'apply?' || destPage == 'application?') {
      if (typeof jobId === 'undefined' || jobId == '') {
        destPage = 'main?';
      } else {
        destPage += 'job_id=' + jobId + '&';
      }
    }

    if (destPage == 'assessment?') {
      if (typeof rmproj === 'undefined' || rmproj == '') {
        destPage = 'main?';
      } else {
        destPage += 'commseqno=' + rmproj + '&';
      }
    }

    if (destPage == 'booking?') {
      if (typeof rmproj === 'undefined' || rmproj == '' || jobId == 'undefined' || jobId == '') {
        destPage = 'main?';
      } else {
        destPage += 'commseqno=' + rmproj + '&job_id=' + jobId + '&';
      }
    }

    // oldstyleurl
    let rmurl = '';
    rmurl = getQueryVariable('RMURL');
    if (typeof rmurl !== 'undefined' && rmurl.length > 0) {
      destPage = 'job?job_id=' + rmurl + '&';
    }
    rmurl = getQueryVariable('rmurl');
    if (typeof rmurl !== 'undefined' && rmurl.length > 0) {
      destPage = 'job?job_id=' + rmurl + '&';
    }

    const rmtoken = getQueryVariable('rmtoken');
    if (destPage == 'subscription?' || destPage == 'subscriptions?') {
      if (typeof rmtoken === 'undefined' || rmtoken == '') {
        destPage = 'main?';
      } else {
        destPage += 'skey=' + rmtoken + '&';
      }
    }

    if (destPage == 'profileactivate?') {
      const skey = getQueryVariable('skey');
      if (typeof skey === 'undefined' || skey == '') {
        destPage = 'main?';
      } else {
        destPage += 'skey=' + skey + '&';
      }
    }

    iFrameUrl += destPage;
    iFrameUrl += 'site=' + site + '&validator=' + validator;

    if (typeof langId === 'undefined' || langId == '') {
      langId = langDef;
    }

    const cantoken = getQueryVariable('cantoken');
    if (cantoken) {
      iFrameUrl += '&cantoken=' + cantoken;
    }

    iFrameUrl += '&lang=' + langId;

    const cookie_name = 'rm_reftracker_1118';
    const cookie_parts = ('; ' + document.cookie).split('; ' + cookie_name + '=');

    if (cookie_parts.length == 2) {
      const cookie_value = cookie_parts
        .pop()
        .split(';')
        .shift();
      if (cookie_value !== '') {
        ref = cookie_value.replace(/^"(.*)"$/, '$1');
      }
    }

    // If referer header is empty, check for QueryString ref
    if (ref == null || ref == undefined || ref == '') {
      ref = getQueryVariable('ref');
    }
    if (ref == undefined || ref.length == 0) {
      ref = '';
    }

    if (ref.length > 0) {
      ref = ref.toLowerCase();
      ref = ref.indexOf('t.co') > -1 ? 'Twitter' : ref;
    }

    iFrameUrl += '&ref=' + ref; // referrer

    // add iHelperURL
    if (iHelperUrl != '' && iHelperUrl != undefined) {
      iFrameUrl += '&ihelper=' + iHelperUrl;
    }

    // Resize iframe to full height
    function resizeIframe(height) {
      // +60 is a general rule of thumb to allow for differences in
      // IE & and FF height reporting, can be adjusted as required..
      document.getElementById('riframe').height = parseInt(height) + 60;
    }

    function scrollToIframe(iframeElement) {
      if (iframeElement.dataset.loadedFirstTime) {
        iframeElement.scrollIntoView(true);
      }
      iframeElement.dataset.loadedFirstTime = true;
    }

    if (!rmihelper) {
      const iframe = document.createElement('iframe');
      iframe.setAttribute('allowTransparency', 'true');
      iframe.setAttribute('title', 'title');
      iframe.setAttribute('id', 'riframe');
      iframe.setAttribute('name', 'riframe');
      // iframe.setAttribute('onload', 'scrollToIframe(this)');
      iframe.setAttribute('width', '100%');
      iframe.setAttribute('height', height);
      iframe.setAttribute('frameborder', '0');
      iframe.setAttribute('src', iFrameUrl);

      const wrap = document.createElement('div');
      wrap.appendChild(iframe);

      // document.write(wrap.innerHTML); // <- Old code
      const modalBody = document.getElementById('job-listings-modal').getElementsByClassName('c-modal__content')[0];

      modalBody.innerHTML = '';
      modalBody.appendChild(wrap);

      // Listen for messages sent from the iFrame
      const eventMethod = window.addEventListener ? 'addEventListener' : 'attachEvent';
      const eventer = window[eventMethod];
      const messageEvent = eventMethod == 'attachEvent' ? 'onmessage' : 'message';

      eventer(
        messageEvent,
        e => {
          if (e.data.indexOf && {}.toString.call(e.data.indexOf) === '[object Function]')
            if (e.data.indexOf('resize::') != -1) {
              const height = e.data.replace('resize::', '');
              document.getElementById('riframe').style.height = parseInt(height) + 'px';
            }
        },
        false
      );
    }
  };
}

new JobListings();
