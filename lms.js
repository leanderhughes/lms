let thisIsMobile = '';
function isMobile(){
  if(thisIsMobile === false || thisIsMobile === true){
    return thisIsMobile;
  }
  let check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  thisIsMobile = check;
  return check;
}

const chime = new Audio('chime.mp3');
const tut = localStorage.getItem('tut') ? localStorage.getItem('tut') : '';
class User {
  constructor(){
    this.data = {};
    this.props = {
      username:1,
      personalName:1,
      familyName:1,
      sid:1,
      email:1,
      loggedIn:1
    }
    const that = this;
    for(let key in that.props){
      this[key] = function(){
        return that.data[key];
      }
    }
  }
  update(res,action){
    if(!res.user){
      return;
    }
    const that = this;
    for(let key in res.user){
    //  if(that.props[key]){
        that.data[key] = res.user[key];
        this[key] = function(){
          return that.data[key];
        }
  //    }
    }
    return true;
  }
}
function timeout(callback,ms){
  let start;
  function step(timestamp) {
    start = start === undefined || start==='' ? timestamp : start;
    const elapsed = timestamp - start;
    if (elapsed >= ms) {
      callback();
      return;
    }
    window.requestAnimationFrame(step);
  }
  step();
}
let loadingIframe = false;
const user = new User();
const header = elem.as('div#header').to(document.body);
const content = elem.as('div#content').to(document.body);
const copyright = elem.as('div#copyright>&copy;'+new Date().getFullYear()+' [Copyright]').to(document.body);
function search(string){
  const before = window.location.href;
  if(window.location.href.indexOf('#'+string)>-1){
    return;
  }
  window.location.href = window.location.href.split('?')[0].split('#')[0]+'#'+string;
  const after = window.location.href;
  console.log('before',before,'VS','after',after);
//  alert();
  //after != before && init();

  return true;
}
window.addEventListener('popstate', init);
function flash(el,count='none'){
  if(count=='none'){
    count = 1;
  }
  count % 2 === 1 ? $(el).hide() : $(el).show();
  count % 2 === 1 ? console.log('hide',count,el) : console.log('show',count,el);
  if(count===0){
    return;
  }
  count--;
  timeout(()=>flash(el,count),count % 2 === 0 ? 50 : 300);
}
function highlightAndFade(el,a=false){
  if(a===false){
    a = 1;
  }
  if(a <= 0){
    return;
  }
  $(el).css('background-color','rgba(100,255,230,'+a+')');
  a-=.01;
  timeout(function(){
    highlightAndFade(el,a);
  },1);
  return true;
}
function handleErrors(res,callback=false,error=false){
  const handle  = {
    loginRequired:function(){search('login')},
    confirmRequired:function(){search('confirm')},
    dash:showLogin,
    dbError:(res)=>{showMessage('Error: Failed Query. '+res.query);}
  };
  $('.warn').removeClass('warn');
  if(res.Error){
    $('#'+res.Error).length && $('#'+res.Error).addClass('warn');
    $('.'+res.Error).length && $('.'+res.Error).addClass('warn');
    res.message ? showMessage(res.message,(r)=>{
      closeMessage();
      error && error(r);
      handle[res.Error] && handle[res.Error](res);
    }) : handle[res.Error] && handle[res.Error](res);
    return;
  }
  if(!res.message){
    callback && callback(res);
    return;
  }
  showMessage(res.message,function(){
    closeMessage();
    callback && callback(res);
  });
}
function parseData(data){
  if(data[0]){
    data.forEach && data.forEach((item,index,array)=>{
      array[index] = parseData(item);
    });
    return data;
  }
  for(let prop in data){
    if(typeof data[prop]=='object'){
      data[prop]=parseData(data[prop]);
      continue;
    }
    if(typeof data[prop] == 'string' && data[prop].match(/\D/)){
      continue;
    }
    if(data[prop]===true || data[prop]===false){
      continue;
    }
    if(!data[prop]){
      continue;
    }
    data[prop] = parseFloat(data[prop]);
  }
  return data;
}
function parseJSON(ob){
  for(let key in ob){
    ob[key] = isStringifiedOb(ob[key]) ? parseStringifiedObArray(ob[key]) : ob[key];
    ob[key] = typeof ob[key] == 'object' ? parseJSON(ob[key]) : ob[key];
  }
  return ob;
}
let sent = {};
function send(action,data={},callback=false,error=false){
  action!='ping' && console.log('send',action,data);
  if(typeof data=='function'){
    callback = data;
    data = {};
  }
  data.action = action;
  action!='ping' && console.log('send action ',data.action);
  $.ajax({
    url: 'lms.php',
    type: 'POST',
    dataType: 'json',
    data:data,
    async:true,
    success:function(res){
      res = parseJSON(res);
      res.action!='ping' && console.log('res for ',data.action,' -> ',res);
      res = parseData(res);
      user.update(res,data.action);
      handleErrors(res,callback,error);
      if(!res.Error){
        sent = res;
      }
      //user.loggedIn() && res.action != 'ping' && send('ping');
    },
    error:function(e){
      showMessage("An error occurred. Please check your internet connection.");
      console.log('send error',e.responseText,e);
    //  data.action!='ping' && send('ping');
    }
  });
}
function getRequest(action,data){
  data.action=action;
  $.ajax({
    url: 'lms.php',
    type: 'GET',
    dataType: 'json',
    data,
    async:true,
    success:function(res){
      console.log('getRequest successfull',data);
    },
    error:function(e){
      console.log('getRequest error',e.responseText,e);
    }
  });
}
function regularTime(decTime){
  let hours =  Math.floor(decTime);
  let mins = Math.round((decTime - hours)*60);
  mins = mins < 10 ? ('0'+mins) : mins;
  let sa = hours > 12 ? 'pm' : 'am';
  hours = hours > 12 ? (hours-12) : hours;
  return hours + ':' + mins + '&nbsp;'+sa;
}
function getLinkAndPass(text){
  if(!text || !text.trim()){
    return {error:'meetingInfo',message:'No class info has been entered.'}
  }
  text = text.trim().replace(/ /g,'').replace(/\n{1,100}/g,'\n').split('\n');
  let link = '';
  let pass = '';
  text.forEach((item,index,array)=>{
    link = !link && array[index-1] && array[index-1] == 'JoinZoomMeeting' ? item.trim() : link;
    pass = !pass && item.indexOf('Passcode:') > -1 ? item.replace('Passcode:','').trim() : pass;
  });
  return link && pass ? {link:link, pass:pass} : {error:'meetingInfo',message:'ERROR: Failed to parse Zoom invitation. Please make sure to copy and paste your zoom info exactly as it is.'}
}
function updateMeeting(day){
  showMessage(
    "To create/update your Zoom class. Copy your Zoom invitation, paste it below, and click 'Update Info.'",
    'div',
    'br',
    {
      textarea:{
        id:'meetingInfo',
        placeholder:'(Paste your Zoom Info here.)'
      }
    },
    'br',
    {
      button:{
        innerHTML: 'Update Info',
        onclick:function(){
          const data = getLinkAndPass($('#meetingInfo').val());
          data.day = day;
          if(data.error){
            handleErrors(data,false,function(){updateMeeting(day)});
            return;
          }
          send('updateMeetingInfo',data,false,function(){updateMeeting(day)});
        }
      }
    },
    {
      button: {
        innerHTML:'Cancel',
        onclick:closeMessage
      }
    }
  );
}
function openMeeting(day){
  $('#openMeetingBtn').hide();
  $('#openMeetingBtn').after(elem.as('span#openingMeeting>(...opening)'));
  send('openMeeting',{day:day,openNow:1},
    function(res){
      loadingIframe = true;
      elem.as({
        iframe:{
          src:res.link,
          style:{
            position:'absolute',
            left:'-1000px',
            top:'-1000px'
          },
          callback:function(el){
            timeout(function(){
              $('#openingMeeting').length && $('#openingMeeting').remove();
              timeout(()=>{
                loadingIframe = false;
                search('schedule');
              },3000);
            },1000);
          }
        }
      }).to(content);
    },
    function(){
      search('schedule');
    }
  );
}
function closeMeeting(){
  send('closeMeeting',{openNow:0},(res)=>{$('#closeMeetingBtn').remove(); timeout(()=>{showSchedule(res)},1000)});
}
function getMeeting(res,callback=false){
  loadingIframe = true;
  elem.as({
    iframe:{
      src:res.link,
      style:{
        position:'absolute',
        left:'-1000px',
        top:'-1000px'
      },
      callback:function(el){
        timeout(function(){
          $('#joinBtn').length && $('#joinBtn').html('Re-Join Class') && $('#joinBtn').show();
          $('#joining').length && $('#joining').remove();
          callback && callback(res);
          timeout(()=>{
            loadingIframe = false;
            $(el).remove();
          },10000);
        },2000);
      }
    }
  }).to(content);
  return true;
}
function joinMeeting(){
  function joinNow(keydownEvent=false){
    if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
      return;
    }
    if(!$('#joinBtn2').length){
      $(window).unbind('keydown.joinNow');
    }
    const reason = $('#reason').val();
    if(!reason || !reason.trim()){
      return;
    }
    $('#joinBtn').hide();
    $('#joinBtn').after(elem.as('span#joining>(...joining)'));
    showMessage('Thank you. Please wait a moment while we connect to the class...');
    send('joinMeeting',{
        reason:reason
      },
      getMeeting,
      getSchedule
    );
  }
  showMessage(
    'Please let us know the reason why you are joining today\'s class.',
    {
      textarea:{
        id:'reason',
        placeholder:'Example: To talk with my friends'
      }
    },
    'br',
    {
      button:{
        innerHTML:'Join Class!',
        id:'joinBtn2',
        style:{
          display:'none'
        },
        callback:function(el){
          $('#reason').keydown(function(){
            $('#reason').val().trim() ? $('#joinBtn2').show() : $('#joinBtn2').hide();
          });
          $(window).bind('keydown.joinNow',joinNow);
        },
        onclick:function(){
          joinNow();
        }
      }
    }
  );
}
function createEditMessage(day,message=''){
  showMessage(
    "Enter/edit a message you would like to be shown beside your class information and click 'Save Message.' (If you wish to delete a pre-existing message, simply delete the text in the box and click 'Save Message.')",
    'br',
    {
      textarea:{
        id:'messageInput',
        value:message
      }
    },
    'br',
    {
      button:{
        innerHTML: 'Save Message',
        onclick:function(){
          send(
            'createEditMessage',
            {
              message: $('#messageInput').val() ? $('#messageInput').val().trim() : "",
              day: day
            },
            function(){
              search('schedule');
            }
          );
        }
      }
    },
    {
      button:{
        innerHTML:'Cancel',
        onclick:closeMessage
      }
    }
  );
}
function formatScheduleDataToShow(res){
  const rows = res.scheduleRows;
  const joinedRows = res.joinedRows;
  const newRows = [];
  rows.forEach(row=>{
    let message = '';
    let meetingOptions = row.openNow ? {button:{id:'joinBtn',innerHTML:'Join Class',onclick:joinMeeting}} : (message ? message : '');
    if(row.isFacilitator && !row.link && !row.alreadyReminded){
      showMessage("Hi, "+user.personalName()+". It looks like you haven't added a Zoom link for your "+row.day+" class yet. Click the 'Update Zoom Info' button to do that.");
    }
    if(row.isFacilitator){
      meetingOptions = !row.openNow && (row.shouldBeOpen || row.openSoon) ? {button:{innerHTML:'Open class',id:'openMeetingBtn',onclick:function(){openMeeting(row.day)}}} : (row.openNow ? {button:{id: 'closeMeetingBtn', innerHTML:'Close class',onclick:closeMeeting}} : '');
      message = row.message ? {div:{id:'myMessage', innerHTML:'(*'+row.message+')',onclick:function(){showMessage("This message will appear next to your class info when students look at the schedule. Click the 'Create/Edit Message' button to edit it.")}}} : '';
    }
    else{
      message = row.message ? '(*'+row.message+')' : (row.shouldBeOpen ? '(*Sorry, this class is not open yet. Please check back again soon.)' : '');
    }
    message = row.closedThisWeek && !row.isFacilitator ? '' : message;
    newRows.push({
      'Day':row.day,
      'Facilitator':row.personalName+' '+row.familyName,
    //  '_Open':regularTime(row.startTime),
      'Hours': regularTime(row.startTime)+' - '+regularTime(row.endTime)+ (row.closedThisWeek ? ' <span class="redNote">(*Closed this week)</span>' : ''),
    //  '_Closes':regularTime(row.endTime),
      '_Meeting Options': meetingOptions ? meetingOptions : (row.isFacilitator ? '' : message),
      '_Update class Button': row.isFacilitator && !tut ? {button:{innerHTML:'Update Zoom Info',onclick:function(){updateMeeting(row.day)}}}:'',
      '_Create/Edit Message Button': row.isFacilitator && !tut ? {button:{innerHTML:'Create/Edit Message',onclick:function(){createEditMessage(row.day,row.message)}}}:'',
      '_Message': row.isFacilitator || (row.openNow && message) ? message : ''
    });
    if(row.isFacilitator && row.openNow){
      const trCount = Object.keys(newRows[newRows.length-1]).length-1;
      newRows.push({'joinedTable':{trCount:trCount},rows:joinedRows});
    }
  });
  return newRows;
}
function topBar(){
  const edit = {
    name:function(){
      function changeName(keydownEvent){
        if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
          return;
        }
        if(!$('#changeName').length){
          $(window).unbind('keydown.changeName');
        }
        const data = {};
        $('input').each(function(){
          data[this.id] = this.value;
        });
        send('changeName',data,()=>search('schedule'));
      }
      showMessage(
        "Would you like to change your name?",
        {
          input:{
            id:'personalName',
            placeholder:'personalName',
            value: user.personalName()
          }
        },
        'span> ',
        {
          input:{
            id:'familyName',
            placeholder:'familyName',
            value: user.familyName()
          }
        },
        'br',
        {
          button:{
            innerHTML:'Change my name',
            id:'changeName',
            onclick:function(){
              changeName();
            },
            callback:function(){
              $(window).bind('keydown.changeName',changeName);
            }
          }
        },
        {
          button:{
            innerHTML:'Cancel',
            onclick:closeMessage
          }
        }
      );
    },
    pwd:function(){
      function changePassword(keydownEvent){
        if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
          return;
        }
        $(window).unbind('keydown.changePassword');
        const data = {};
        $('input').each(function(){data[this.id]=this.value;});
        send('changePassword',data,()=>search('schedule'));
      }
      showMessage(
        "Would you like to change your password?",
        {
          input:{
            id:'currentPassword',
            placeholder:'Current Password',
            type:'password'
          }
        },
        'br',
        {
          input:{
            id:'password',
            placeholder:'New Password',
            type:'password'
          }
        },
        'br',
        {
          input:{
            id:'password2',
            className:'newPassword',
            placeholder:'New Password (again)',
            type:'password'
          }
        },
        'br',
        {
          button:{
            id:'changePassword',
            className:'newPassword',
            innerHTML:'Change my password',
            onclick:function(){
              changePassword();
            },
            callback:function(){
              $(window).bind('keydown.changePassword',changePassword);
            }
          }
        },
        {
          button:{
            innerHTML:'Cancel',
            onclick:closeMessage
          }
        }
      );
    },
    sid:function(){
      function changeSid(){
        const data = {};
        $('input').each(function(){data[this.id]=this.value});
        send('changeSid',data,()=>search('schedule'));
      }
      changeSid = addOnEnter('changeSid',changeSid);
      showMessage(
        'Would you like to change your Student ID?',
        {
          input:{
            id: 'sid',
            className:'sid',
            placeholder:'New Student ID'
          }
        },
        'br',
        {
          input:{
            id: 'sid2',
            className:'sid',
            placeholder:'New Student ID (again)',
          }
        },
        'br',
        {
          button:{
            id:'changeSid',
            innerHTML:'Change my student ID',
            onclick:changeSid
          }
        },
        {
          button:{
            innerHTML:'Cancel',
            onclick:closeMessage
          }
        }
      );
    }
  }
  function userInfo(){
    return [
      {
        div:{
          id:'userInfo'
        }
      },
      1,
      'span.link editable#name>'+user.personalName()+' '+user.familyName(),
      'span> ',
      user.sid()=='(teacher)' ? 'span' : 'span.link editable#sid>('+user.sid()+')',
      'span> ',
      'span.link editable#pwd>&#x26bf;'

    ];
  }
  return [
    'div',
    'h1',
    1,
    {
      img:{
        src:"universitylogo.png",
        style:{
          marginLeft:"-20px",
          marginRight:"4px",
          opacity:.7,
          height:'22px'
        }
      }
    },
    'span>My Courses Online',
    -1,
    'div#topBar',
    1,
    ...(user.loggedIn() ? userInfo() : ['span']),
    !user.loggedIn() && window.location.search=='?login' ? 'span' : {
      div:{
        innerHTML:user.loggedIn() ? 'Log Out' : 'Log In',
        id:user.loggedIn() ? 'logout' : 'login',
        className:'link',
        onclick:function(){
          user.loggedIn() ? logOut() : showLogin();
        }
      }
    },
    {
      span:{
        callback:function(){
          $('.editable').click(function(){edit[this.id]()});
        }
      }
    }
  ];
}
function isFacilitating(res){
  const rows = res.scheduleRows;
  for(let i=0,l=rows.length;i<l;i++){
    const row = rows[i];
    if(row.isFacilitator && row.openNow){
      return true;
    }
  }
  return false;
}
let newJoinedCheckRequired = false;
let checkingForNewJoined = false;
function stopCheckingForNewJoined(){
  newJoinedCheckRequired = false;
}
function startCheckingForNewJoined(){
  newJoinedCheckRequired = true;
  if(!checkingForNewJoined){
    checkForNewJoined();
  }
}
function checkForNewJoined(){
  if(!newJoinedCheckRequired){
    checkingForNewJoined = false;
    return;
  }
  checkingForNewJoined = true;
  send(
    'checkForNewJoined',
    function(res){
      res.newJoined && !loadingIframe && search('schedule');
      timeout(checkForNewJoined,10000);
    }
  );
}
let checkingForScheduleUpdate = false;
function startCheckingForScheduleUpdate(){
  if(checkingForScheduleUpdate){
    return;
  }
  checkingForScheduleUpdate = true;
  timeout(checkForScheduleUpdate,60000);
}
function checkForScheduleUpdate(){
  send(
    'checkForSchduleUpdate',
    function(res){
      res.newUpdate && !loadingIframe && search('schedule');
      timeout(checkForScheduleUpdate,60000);
    }
  );
}
var specialAnnouncement = ``;//event poster html here
function showCourses(res=false){
  if(!res){
    send('getCourses',showCourses);
    return;
  }
  const courseEls = [];
  res.courses && res.courses.length && res.courses.forEach(course=>{
    courseEls.push(
      {
        li:{
          className: 'bigLink',
          innerHTML: `${course.title} (${course.course}) - ${course.dayPeriod}`,
          onclick:function(){
            send('selectCourse',{course:course.course},dash);
          }
        }
      }
    );
  });
  courseEls.length && courseEls.unshift('ul',1);
  !courseEls.length && courseEls.push('(No courses available right now...)');
  elem.as([
    'div',
    'h2>Select your course',
    'h3>(NOTE: Classes are currently being held in person. Please come to the classroom at Saitama University to join.)',
    ...courseEls
  ]).to(content);
  if(specialAnnouncement){
    $(content).append(specialAnnouncement);
  }
  return true;
}

function getQuill(idProp){
  const saved = document.getElementById(idProp).innerHTML;
  const quill = $(`#${idProp}.ql-container`).length ? '' : new Quill(`#${idProp}`, {
    theme: 'snow'
  })
  quill && $(`#${idProp}`).after(elem.as({
    button:{
      innerHTML:'Cancel',
      className:`edit ${idProp}`,
      onclick:function(){
        console.log('saved',saved);
        document.getElementById(idProp).className = '';
        document.getElementById(idProp).innerHTML = saved;
        $(`#${idProp}`).parent().find(`.ql-toolbar,.edit.${idProp}`).remove();
      }
    }
  }));
  quill && $(`#${idProp}`).after(elem.as({
    button:{
      innerHTML: 'Update',
      className:`edit ${idProp}`,
      onclick: ()=>{

        send('updateCourse',{
          [idProp]:$(`#${idProp}`).find('.ql-editor').html()
        },res=>{
          document.getElementById(idProp).className = '';
          document.getElementById(idProp).innerHTML = res[idProp];
          $(`#${idProp}`).parent().find(`.ql-toolbar,.edit.${idProp}`).remove();
        });
      }
    }
  }));
}

function editIcon(idProp){
  return {
    span:{
      className:'clickable',
      innerHTML:'&#9998;',
      onclick:()=>{
        getQuill(idProp);

      }
    }
  }
}

function editSurveys(){
  return {
    span:{
      className:'clickable',
      innerHTML:'&#9998;',
      onclick:()=>{
        send('editSurveys',res=>{
          elem.clear($('#assignments')[0]);
          $('#assignments').append(elem.as(['div',...getAssignmentElements(res)]));
        });
      }
    }
  }
}

function getAssignmentElements(res){
  const assEls = [];
  res.assignments && res.assignments.length && assEls.push('table',1) && res.assignments.forEach(ass=>{
    ass.complete && assEls.push(
      'tr',
      1,
      {
        td:{
          innerHTML:`<strong>${ass.title}</strong> (&#10004; complete!)`,
          style:{
            cursor:'pointer'
          },
          onclick:()=>{
            showMessage("You've already completed this survey/quiz.");
          }
        }
      },
      -1
    );
    !ass.complete && assEls.push(
      'tr',
      1,
      {
        td:{
          className:'bigLink',
          innerHTML: ass.title,
          onclick:()=>{
            showSurvey(res.course,ass.title);
          }
        }
      },
      !user.isAdmin() ? 'td' : {
        td:{
          className:'link',
          innerHTML: ass.open ? 'lock' : 'unlock',
          onclick:function(){
            send(ass.open ? 'lockSurvey' : 'unlockSurvey', {title:ass.title}, res=>{
              ass.open = !ass.open;
              this.innerHTML = ass.open ? 'lock' : 'unlock';
            });
          }
        }
      },
      !user.isAdmin() ? 'td' : {
        td:{
          className:'link',
          innerHTML: ass.published ? 'unpublish' : 'publish',
          onclick:function(){
            send(ass.published ? 'unpublishSurvey' : 'publishSurvey', {title:ass.title}, res=>{
              ass.published = !ass.published;
              this.innerHTML = ass.published ? 'unpublish' : 'publish';
            });
          }
        }
      },
      -1
    );
  });
  assEls.length && assEls.push(-1);
  !assEls.length && assEls.push('p>(No new surveys/quizzes)');
  return assEls;
}

function dash(res=false){
  if(!res){
    send('dash',dash);
    return;
  }
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  if(!res.course){
    showCourses();
    return;
  }
  if(window.location.search.match(/#showSurvey/)){
    showSurvey();
  }
  //res = parseJSON(res);
  //console.log('links',res.links,res.links ? parseJSON(res.links) : '');
  elem.clear(content);
  const assEls = getAssignmentElements(res);
  const linkEls = [];
  res.links && Array.isArray(res.links) && res.links.length && res.links.forEach(link=>{
    linkEls.push({div:{style:{marginBottom:'10px'}}},1,{
      a:{
        className:'medLink',
        innerHTML:link.title,
        href:link.url,
        target:'_blank'
      },
    },-1);
  });
  res.links && !Array.isArray(res.links) && linkEls.push({
    div:{
      innerHTML: res.links
    }
  });
  const cancelled = new Date().getTime() < 1640617199000 && ['GES','AES'].includes(res.course);
  !linkEls.length && linkEls.push('span');
  console.log({linkEls});
  elem.as([
    'div',`h2>${res.course}`,
    'h3>NOTE: Classes are currently being held in person. Please come to the classroom at Saitama University to join.',
    {
      button:{
        className:'submit',
        innerHTML: cancelled ? '(Class is cancelled today. See you next time!)' : 'Share screen with class',//Join class through Zoom
        callback:function(el){
          el.disabled = cancelled;
        },
        onclick:function(){
          send('getZoomLink',res=>{
            showMessage("",[
              'div',
              'p>When you enter the class please: ',
              'br',
              'ol',
                1,
                {li:{style:{textAlign:'left',marginBottom:'20px'}}},
                  1,
                  `span>Change your Zoom name to: `,
                    1,
                    {
                      div:{
                        innerHTML: `${user.personalName()} ${user.familyName()} ${user.sid()}`,
                        style:{
                          fontWeight:'bold',
                          whiteSpace:'nowrap',
                          width:'100%',
                          textAlign:'center',
                          paddingTop:'10px'
                        }
                      }
                    },
                  -1,
                -1,
                {li:{innerHTML:'Turn your video on.',style:{textAlign:'left',marginBottom:'20px'}}},
              -1,
            'br',
            'p>The passcode is: ',
              1,
              `em>${res.pass}`,
            -1,
            {
              button:{
                innerHTML:'OK',
                onclick:()=>{
                  if(isMobile()){
                    window.location.href = res.zoomLink;
                    return;
                  }
                  elem.as({
                    iframe:{
                      src: res.zoomLink,
                      style:{
                        position:'absolute',
                        left:'-1000px',
                        top:'-1000px',
                        width:'10px',
                        height:'10px'
                      },
                      callback:(el)=>{
                        $(el).prop('disabled',true);
                        timeout(closeMessage,1500);
                        timeout(()=>{
                          $(el).remove();
                          $(el).prop('disabled',false);
                        },3000);
                      }
                    }
                  }).to(content);
                }
              }
            }
            ],);
          });
        }
      }
    },
    'hr',
    'h3>New Surveys/Quizzes',
    1,
      !user.isAdmin() ? 'span' : editSurveys(),
     -1,
    'div#assignments',
    1,
      ...assEls,
      !res.gradesAvailable ? 'span' : {
        a:{
          innerHTML:'Grades',
          href:'#showGrades',
          style:{
            margin:'40px auto',
            display:'table'
          }
        }
      },
    -1,
    'br',
    'br',
    'hr',
    'h3>Homework',
    1,
      !user.isAdmin() ? 'span' : editIcon('homework'),
    -1,
    res.homework ? 'p#homework>'+res.homework : 'p>(coming soon...)',
    'br',
    'br',
    'hr',
    'h3>Important Links',
    1,
      !user.isAdmin() ? 'span>' : editIcon('links'),
    -1,
    'p#links',
    1,
      ...linkEls,
    -1
  ]).to(content);

}
function saveSurvey(course='(no course)',title='(no title)',timeLimit=0){
  function getCorrect(item){
    item.correctResponse = item.choices[0];
    return item;
  }
  const fillIns = ['title','course'];
  const survey = [
    {
      title: title,
      course:course,
      timeLimit:timeLimit,
      item:{
        content:
        `VOCABULARY

A(n) ____ sells goods.`,
        randomizeItems:1,
        randomizeChoices:1,
        itemsPerPage:1
      },
      choices: [
        `vendor`,
        `consumer`,
        `host`,
        `explorer`
      ]
    },
    {
      title: title,
      course:course,
      timeLimit:timeLimit,
      item:{
        content:
        `TACTICS

Which question should you answer LAST?`,
        randomizeItems:1,
        randomizeChoices:1,
        itemsPerPage:1
      },
      choices: [
        `Which of the following is NOT true about the refund policy?`,
        `How will payments in cash be refunded in the case of a withdrawal?`,
        `What is required for a refund due to a medical reason?`,
        `How much can be refunded after the second lesson?`
      ]
    },
    {
      title: title,
      course:course,
      timeLimit:timeLimit,
      item:{
        content:`VOCABULARY

Smoking in the classroom in the classroom is ______ at Saitama University.`,
        randomizeItems:1,
        randomizeChoices:1,
        itemsPerPage:1
      },
      choices: [
        `prohibited`,
        `revised`,
        `culinary`,
        `complimentary`
      ]
    }
  ];
  survey.forEach((item,index,array)=>{
    array[index] = getCorrect(item);
    array[index].type = 'quiz';
  });
  const fillInData = {};
  const surveyRows = [];
  const questionTypes = {
    ordering:1
  };
  survey.forEach(item=>{
    item.isQ = !item.item.type || questionTypes[item.item.type] ? 1 : 0;
    const row = {};
    for(let key in item){
      if(fillIns.indexOf(key) > -1){
        fillInData[key] = item[key];
      }
      if(typeof item[key] == 'object'){
        item[key] = JSON.stringify(item[key]);
      }
      row[key] = item[key];
    }
    fillIns.forEach(fillIn=>{
      row[fillIn] = row.hasOwnProperty(fillIn) ? row[fillIn] : fillInData[fillIn];
    });

    surveyRows.push(row);
  });
  send('saveSurvey',{surveyRows:surveyRows},res=>console.log(res));
}
function isStringifiedOb(ob){
  if(typeof ob != 'string'){
    return false;
  }
  const sides = ob.slice(0,1)+ob.slice(-1);
  return sides == '{}' || sides == '[]';
}
function parseStringifiedObArray(ob){
  ob = ob.replace(/[\n\r]/g,'\\n');
  if(ob[0]=='{'){
    try {
      return JSON.parse(ob)
    } catch(error){
      console.log('failed parse: ',ob,'\n',error.message);
      return '...failed';
    };
  }
  const tempOb = '{"res":'+ob+'}';
  try {
    return (JSON.parse(tempOb)).res;
  } catch(error){
    console.log('failed parse: ',tempOb.res,'\n',error.message);
    return 'failed...';
  };
}

class Events {
  constructor(){
    this.onces = {};
    this.ons = {};
    this.calledTime = {};
    this.timeouts = {};
    this.happened ={};
    this.onStricts = {};
  }
  has(name){
    return this.happened[name];
  }
  dispatch(name,detail=false){
    const event = detail ? new CustomEvent(name,{detail:detail}) : new CustomEvent(name);
    window.dispatchEvent(event);
    this.happened[name]=true;
    return true;
  }
  on(name,handler){
    const thisHandler = function(res=''){
      res && res.detail ? handler(res.detail) : handler(res);
    }
    this.ons[handler] = thisHandler;
    window.removeEventListener(name,thisHandler);
    window.addEventListener(name,thisHandler);
    return true;
  }
  off(name,handler){
    const thisHandler = function(res=''){
      res && res.detail ? handler(res.detail) : handler(res);
    }
    window.removeEventListener(name,thisHandler);//thisHandler);
    if(this.ons[handler]){
      window.removeEventListener(name,this.ons[handler]);
    }
    return true;
  }
  once(name,handler){
    if(this.onces[name] && this.onces[name][handler]){
      return true;
    }
    const that = this;
    const handler2 = function(res){
      res && res.detail ? handler(res.detail) : handler(res);
      window.removeEventListener(name,handler2);//that.off(name,handler2);
      that.onces[name][handler]=0;
    }
    this.onces[name] = this.onces[name] ? this.onces[name] : {};
    this.onces[name][handler] = 1;
    window.addEventListener(name,handler2);
    return true;
  }
  onceEvery(name,handler,period){
    if(this.onces[name] && this.onces[name][handler]){
      return true;
    }
    const that = this;
    const handler2 = function(res){
      that.calledTime[name] = that.calledTime[name] ? that.calledTime[name] : {};
      that.calledTime[name][handler] = that.calledTime[name][handler] ? that.calledTime[name][handler] : 0;
      const then = that.calledTime[name][handler];
      const now = new Date().getTime();
      if(now - then < period){
        that.timeouts[name] && that.timeouts[name][handler] && clearTimeout(that.timeouts[name][handler]);
        that.timeouts[name] = that.timeouts[name] ? that.timeouts[name] : {};
        that.timeouts[name][handler] = setTimeout(handler2,10);
        return true;
      }
      that.calledTime[name][handler] = now;
      res && res.detail ? handler(res.detail) : handler(res);
      that.off(name,handler2);
      that.onces[name][handler]=0;
    }
    this.onces[name] = this.onces[name] ? this.onces[name] : {};
    this.onces[name][handler] = 1;
    window.addEventListener(name,handler2);
    return true;
  }
}
const events = new Events();

class Timer {
  constructor(){

  }
  secondsToTimeString(sec_num){
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);
    if (hours   < 10) {hours   = !hours ? '' : "0"+hours;}
    if (minutes < 10) {minutes = !hours && !minutes ? '00' : "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    if(hours && minutes && seconds){
      return hours+':'+minutes+':'+seconds;
    }
    return minutes+':'+seconds;
  }
  start(seconds=false){
    const that = this;
    that.startTime = false;
    that.seconds = seconds;
    elem.as(
      {
        div:{
          id:'timer',
          innerHTML:'Time Left: ',
          style:{
            fontSize:'40px',
            padding:'10px 10px 10px 10px',
            position:'fixed',
            zIndex:1400,
            top:"90px",
            right:"10px",
            backgroundColor:'black',
            color:'white',
            opacity:.3
          },
          onclick:function(){
            user.isAdmin() && timer.stop();
          }
        },
      },
      1,
      {
        span:{
          innerHTML:that.secondsToTimeString(seconds),
          callback:function(el){
            that.timer = el;
            window.requestAnimationFrame((timestamp)=>that.tick(timestamp));
            const toggle = ()=>{
              $('#timer').css('top') == '200px' ? $('#timer').css('top','90px') : $('#timer').css('top','200px');
            }
            $('#timer').mouseover(()=>{
              !user.isAdmin() && toggle();
              timeout(toggle,1500);
            });
          }
        }
      }
    ).to(document.body);
  }
  tick(timestamp){
    const that = this;
    if(that.stopped){
      that.stopped=false;
      that.ticking = false;
      $('#timer').remove();
      return;
    }
    that.ticking = true;
    that.startTime = that.startTime !== false ? that.startTime : timestamp;
    const elapsed = Math.round((timestamp - that.startTime)/1000);
    const timeLeft = that.seconds - elapsed < 0 ? 0 : that.seconds - elapsed;
    that.timer.innerHTML = that.secondsToTimeString(timeLeft);
    if(!that.warned && timeLeft < that.seconds / 4){
      flash($('#timer')[0]);
      $('#timer').css('background-color','darkRed');
      that.warned = true;
    }
    if(timeLeft > 0){
      window.requestAnimationFrame((timestamp)=>that.tick(timestamp));
      return;
    }
    timeout(()=>{
      $('#timer').remove();
      that.ticking=false;
    },500);
    that.warned = false;
    events.dispatch('timeUp');
  }
  stop(){
    this.stopped = this.ticking ? true : this.stopped;
  }
}

const timer = new Timer();

function titleCase(str){
  if(!str || typeof str != 'string'){
    return str;
  }
  return str.split(' ')
     .map(w => w[0].toUpperCase() + w.substr(1).toLowerCase())
     .join(' ');
}

function at(string){
  return window.location.href.indexOf('#'+string) > -1;
}

function showAllGrades(res=false){
  if(res===false){
    showMessage(`Paste your student IDs into the textarea below and click 'Get Grades'.<br><textarea id="sids"></textarea>`,
      {
        button:{
          innerHTML:'Get Grades',
          onclick:function(){

            send('showAllGrades',{
              sids: $('#sids').val() && $('#sids').val().trim() ?  $('#sids').val().trim().replace(/\W/g,',').split(',') : ''
            },showAllGrades);
          }
        }
      }
    );
    return;
  }
  closeMessage();
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  console.log(res.grades);
  console.log('grades',[
    'div',
    'table',
    ...res.grades.reduce((rows,r)=>{
      
      rows.push(...['tr',...r.map(t=>{
        return `td>${t}`;
      })]);
      return rows;
    },[])
  ]);
  elem.as([
    'div',
    {
      button:{
        innerHTML:'Export as CSV file',
        onclick:()=>{
          exportTableToCSV('#allGrades',`grades_for_${res.course}.csv`);
        }
      }
    },
    'table#allGrades',
    ...res.grades.reduce((rows,r)=>{
      
      rows.push(...['tr',...r.map(t=>{
        return `td>${t}`;
      })]);
      return rows;
    },[])
  ]).to(content);

}

function showGrades(res=false){
  if(user.username()=='hughes'){
    showAllGrades();
    return;
  }
  if(res===false){
    send('showGrades',showGrades);
    return;
  }
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  const gradeEls = [];
  res.grades && res.grades.length && res.grades.forEach(grade=>{
    gradeEls.push(
      'tr',
        1,
          `td>${grade.title}`,
          `td>${grade.points} / ${grade.pointsPossible}`,
        -1
      );
  });
  gradeEls.length && gradeEls.unshift('div','h3>Grades','table',1);
  !gradeEls.length && gradeEls.push('div','h3>Grades','div>(No grades for this grading period yet...)');
  elem.as(gradeEls).to(content);
}

function addBreaks(string){
  if(!string){
    return string;
  }
  return string.replace(/[\n\r]/g,'<br />');
}

function itemContent(item){
  if(typeof item == 'string'){
    return item;
  }
  return item.content;
}

function appendSubPage(el,els){

}

function showResponses(el,res){
  const items = [];
  res.surveyRows.forEach(survey=>{
    const item = {
      content: itemContent(survey.item)
    };
    res.responseRows.forEach(response=>{
      if(survey.id==response.itemId){
        const choices = [];
        survey.choices && survey.choices.forEach(choice=>{
          choices.push({
            content: itemContent(choice),
            correct: choice == survey.correctResponse,
            chosen: choice == response.response
          });
        });
        item.choices = choices;
      }
    });
    items.push(item);
  });
  showInSubWindow(el,[
    ...items.reduce((els,item)=>{
      els.push(
        `p.item>${item.content}`,
        1,
          'ul',
          1,
            ...item.choices.reduce((choiceEls,choice)=>{
              choiceEls.push({
                li:{
                  innerHTML:choice.content,
                  className:[choice.correct ? 'correct' : '',choice.chosen ? 'chosen' : ''].join(' ').trim()
                }
              });
              return choiceEls;
            },[]),
          -1,
        -1
      );
      return els;
    },[])
  ]);
  return true;
}

function showSurvey(course=false,title=false){
  console.log('showSurvey',course ? {search:{course,title}} : {});
  if(course===false){
    send('showSurvey',showSurvey);
    return;
  }
  if(typeof course == 'string'){
    send('getSurvey',{search:{course,title}},(res)=>{
      at('showSurvey') ? showSurvey(res) : search('showSurvey');
    });
    return;
  }
  const res = course;
  elem.clear(header);
  elem.as(topBar()).to(header);
  elem.clear(content);
  if(res.action == 'showSurvey'){
    const info = res.surveyInfo;
    elem.as(
      'div',
      `h2>${info.course}`,
      `h3>${info.title}`,
      {
        button:{
          className:'submit',
          innerHTML:'Start '+titleCase(info.type),
          onclick:function(){
            send('startSurvey',showSurvey);
          }
        }
      }
    ).to(content);
    user.isAdmin() && res.results && res.results.length && res.results.sort((a, b) => {return a.sid.localeCompare(b.sid)});
    user.isAdmin() && res.results && res.results.length && elem.as([
        'div',
        {
          p:{
            className:'bigLink',
            innerHTML: 'Show Results',
            onclick:function(){
              this.innerHTML=='Show Results' ? $('#results').show() : $('#results').hide();
              this.innerHTML = this.innerHTML == 'Show Results' ? 'Hide Results' : 'Show Results';
            }
          }
        },
        {
          table:{
            id:'results',
            style:{
              display:'none'
            }
          }
        },
        1,
        ...res.results.reduce((results,r)=>{
          results.push(...[
            'tr',
            1,
            `td>${r.personalName}`,
            `td>${r.familyName}`,
            `td>${r.sid}`,
            `td>${r.points}/${info.points}`,
            {
              td:{
                className:'link',
                innerHTML:'details',
                onclick:function(){
                  const that = this;
                  console.log('el',that,that.getBoundingClientRect().top);
                  send('showResponses',{username:r.username},(res)=>showResponses(that,res));
                }
              }
            },
            {
              td:{
                className:'link',
                innerHTML:'delete',
                onclick:function(){
                  showMessage("Are you sure you want to delete this?",
                    {
                      button:{
                        innerHTML:'Delete',
                        onclick:()=>{
                          send('deleteResponse',{username:r.username},()=>{
                            $(this).parent().css('opacity',.2);
                          });
                        }
                      }
                    },
                    {
                      button:{
                        innerHTML:'Cancel',
                        onclick:closeMessage
                      }
                    }
                  );
                }
              }
            },
            -1
          ]);
          return results;
        },[]),
        -1
    ]).to(content);
    return;
  }
  console.log('before parse ->',res.surveyRows);
  if(!res.surveyRows || !res.surveyRows.length){
    elem.as('p>Survey could not be found...').to(content);
    return;
  }
  let itemIndex = 0;
  const info = res.surveyInfo;
  const surveyRows = res.surveyRows;//parseJSON(res.surveyRows);
  const items = [];
  if(surveyRows[0].itemOrder){
    surveyRows.sort((a,b)=>{return a.itemOrder - b.itemOrder;});
  }
  ['itemsPerPage','randomizeItems'].forEach(key=>{
    if(surveyRows[0].item && surveyRows[0].item[key]){
      info[key] = surveyRows[0].item[key];
    }
  });
  console.log('info',info);
  console.log('after parse ->',surveyRows);

  console.log('surveyRows ->',surveyRows);
  if(info.randomizeItems){
    surveyRows.sort((a,b)=>{return Math.random()-.5;});
  }
  let customSubmit = '';
  const survey = elem.as('div#survey').to(content);
  surveyRows.forEach((row,index)=>{
    if(row.item.type == 'submit'){
      customSubmit = row;
      return;
    }
    if(row.item.type == 'heading'){
      elem.as('h2>'+item.content);
      return;
    }
    const p = elem.as(
      {
        p:{
          innerHTML:(user.isAdmin() ? `<h4>(${row.correctCount} / ${row.responseCount})</h4>` : '')+(row.item.content ? row.item.content : row.item).replace(/[\n\r]/g,'<br />'),
          className:'item'+(row.item.required ? ' required' : ''),
          style:{
            display:row.item.includeOnChosen ? 'none' : ''
          },
          callback:function(el){
            $(el).data('id',row.id);
            $(el).data('item',row.item.content ? row.item.content : row.item);
            $(el).data('index',index);
            row.item.includeOnChosen && $(el).removeClass('required') && events.on('chosen',(res)=>{
              if(res.chosen==row.item.includeOnChosen){
                $(el).show();
                highlightAndFade(el);
                row.item.required && $(el).addClass('required');
              }
              else if(res.chosens.indexOf(row.item.includeOnChosen)>-1){
                $(el).hide();
                $(el).removeClass('required');
                $(el).find('.choice').removeClass('selected');
              }
            });
          }
        }
      }
    );
    const choiceEls = [];
    const jClass = '.choice.item_'+row.id;
    const chosens = [];
    row.item.randomizeChoices && row.choices.sort((a,b)=>{return Math.random()-.5;});
    row.choices && row.choices.length && row.choices.forEach(choice=>{
      choice.chosen && chosens.push(choice.chosen);
    });
    row.choices && row.choices.length && row.choices.forEach(choice=>{
      const choiceContent = (choice.content ? choice.content : choice).replace(/[\n\r]/g,'<br />');
      user.isAdmin() && choiceEls.push({
        li:{
          innerHTML: '('+(row.choiceCount[choiceContent] ? row.choiceCount[choiceContent] : 0)+')',
          style:{
            color:'#88e',
            listStyle:'none',
            width:'100%',
            textAlign:'center'
          }
        }
      });
      choiceEls.push({
        li:{
          innerHTML:choiceContent,
          className: 'choice item_'+row.id,
          onclick:function(){
            $(jClass).removeClass('selected');
            $(this).addClass('selected');
            chosens.length && events.dispatch('chosen',{chosens,chosen:choice.chosen});
          }
        }
      });
    });
    choiceEls.length && elem.as([
      'div',
      p,
      1,
      'ul',
      ...choiceEls
    ]).to(survey);
    items.push(p);
    info.itemsPerPage==1 && index && $(p).hide();
  });
  function submit(){
    timer.stop();
    $('#survey').hide();
    const data = [];
    const unanswered = [];
    $('.item').each(function(){
      const item = this;
      const dat = {
        id:$(this).data('id'),
        itemId:$(this).data('item'),
        response:''
      };
      $(this).find('.choice').each(function(){
        if($(this).hasClass('selected')){
          const val = this.innerHTML;
          dat.response = dat.response !== '' && !Array.isArray(dat.response) ? [dat.response] : dat.response;
          dat.response != '' && data.response.push(val);
          dat.response = dat.response === '' ? val : dat.response;
        }
      });
      dat.response==='' && $(item).hasClass('required') && unanswered.push(item);
      data.push(dat);
    });
    console.log({data});
    if(unanswered.length){
      unanswered.forEach(item=>highlightAndFade(item));
      showMessage("Please answer the required item"+(unanswered.length>1?'s':'')+".");
      return;
    }
    send('submitResponse',{response:data},res=>showMessage(customSubmit ? customSubmit.message : "Successfully submitted!",()=>send('selectCourse',dash)));
  }
  const navBar = info.itemsPerPage!=1 || items.length==1 ? '' : elem.as({
    div:{
      style:{
        margin:'30 auto',
        display:'table'
      }
    }
  }).to(survey);
  const backButton = !navBar ? '' : elem.as({
    button:{
      innerHTML:'Back',
      className:'navButton',
      id:'backButton',
      style:{
        display:'none'
      },
      onclick:function(){
        const that = this;
        itemIndex--;
        items.forEach(item=>{
          $(item).data('index')!=itemIndex ? $(item).hide() : $(item).show();
          !itemIndex && $(that).hide();
          $('#nextButton').show();
          $('#submitButton').hide();
        });
      }
    }
  }).to(navBar);
  const nextButton = !navBar ? '' : elem.as({
    button:{
      innerHTML:'Next',
      className:'navButton',
      id:'nextButton',
      onclick:function(){
        const that = this;
        itemIndex++;
        items.forEach(item=>{
          $(item).data('index')!=itemIndex ? $(item).hide() : $(item).show();
          itemIndex == items.length-1 && $(that).hide() && $('#submitButton').show();
          $('#backButton').show();
        });
      }
    }
  }).to(navBar);
  const submitButton = !navBar ? '' : elem.as({
    button:{
      innerHTML:'Submit',
      className:'navButton',
      id:'submitButton',
      style:{
        display:'none'
      },
      onclick:function(){
        showMessage(`Are you sure you want to submit this ${info.type}`,{button:{innerHTML:'Submit',onclick:submit}},{button:{innerHTML:'Cancel',onclick:closeMessage}});
      }
    }
  }).to(navBar);
  !navBar && elem.as(
    {
      button:{
        innerHTML:customSubmit ? customSubmit.content : `Submit ${titleCase(surveyRows[0].type)}`,
        className:'submit',
        style:{
          display:info.itemsPerPage==1 && items.length>1 ? 'none' :''
        },
        onclick:submit
      }
    }
  ).to(survey);
  info.timeLimit && elem.as({
    div:{
      callback:function(el){
        timer.start(info.timeLimit);
        events.on('timeUp',()=>{
          $('#survey').hide();
          showMessage("Time up!");
          timeout(submit,1000);
        });
      }
    }
  }).to(content);
}
function showSchedule(res){
  const facilitating = isFacilitating(res);
  facilitating ? startCheckingForNewJoined() : stopCheckingForNewJoined();
  startCheckingForScheduleUpdate();
  const nextOpenDate = res.nextOpenDate;
  rows=formatScheduleDataToShow(res);
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  const schedTop = elem.as(
    'div',
    "p>Below is the current schedule. When a class is open, a 'Join Class' button will appear next to the class info. Click the button to join!",
    nextOpenDate ? 'p>My Courses will next be open on <strong>'+nextOpenDate+'</strong>. Hope to see you then!' : 'span',
    'br'
  ).to(content);
  const table = elem.as('table').to(schedTop);
  const headingTr = elem.as('tr').to(table);
  for(let key in rows[0]){
    const th = elem.as(
      'th'+(key[0]=='_' ? '' : ('>'+key))
    ).to(headingTr);
  }
  rows.forEach((row,index)=>{
    const tr = elem.as('tr').to(table);
    if(row.joinedTable){
      const joinedRows = res.joinedRows;
      const joinedTableContainer = elem.build(
        {
          td:{
            colSpan: row.joinedTable.trCount
          },
        },
        {
          div:{
            innerHTML:'Join Attempts for this class',
            id:'joinedContainer'
          }
        }
      );
      tr.appendChild(joinedTableContainer[0]);
      const joinedTable = elem.as({
        table:{
          id:'joined',
          callback:function(el){
            el.scrollIntoView();
          }
        }
      }).to(joinedTableContainer[1]);
      const jThTr = elem.as('tr').to(joinedTable);
      for(let key in joinedRows[0]){
        if(key[0]=='_'){
          continue;
        }
        elem.as('th>'+key).to(jThTr);
      }
      joinedRows.forEach(item=>{
        const jTr = elem.as({
          tr:{
            callback:function(el){
              item._recent && highlightAndFade(el) && chime.play();
            }
          }
        }).to(joinedTable)
        for(let key in item){
          if(key[0]=='_'){
            continue;
          }
          elem.as('td>'+item[key]).to(jTr);
        }
      });
      return true;
    }
    for(let key in row){
      const td = elem.as(typeof row[key] == 'object' ? 'td' : 'td>'+row[key]).to(tr);
      if(typeof row[key] == 'object'){
        const el = elem.as(row[key]).to(td);
      }
    }
  });
}
function showAccountConfirmed(res){
  elem.clear(content);
  elem.as(
    'div',
    'h2>Account Confirmed!',
    'p>Welcome, '+res.userInfo.personalName+', to My Courses Online.',
    'p>Your username is: <strong'+(tut ? 'class="blurred"' : '')+'>'+res.userInfo.username+'</strong>',
    'p>Please use it along with the password you made to log in on your next visit.',
    {
      button:{
        innerHTML: 'OK, Got it!',
        onclick:function(){
          search('schedule');
        }
      }
    }
  ).to(content);
}
function showConfirmAccount(res){
    function confirmAccount(keydownEvent=false){
      if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
        return;
      }
      if(!$('#confirmationCode').length){
        $(window).unbind('keydown.confirmAccount');
      }
      const confirmationCode = $('#confirmationCode').val() ? $('#confirmationCode').val().trim() :  '';
      send('checkConfirmation',{confirmationCode:confirmationCode},showAccountConfirmed);
    }
    elem.clear(content);
    elem.clear(header);
    elem.as(topBar()).to(header);
    elem.as(
      'div',
      'h2>Account Confirmation',
      'p>A confirmation code has been sent to <strong>'+res.to+'</strong> (Please check your spam folder if it is not in your inbox)',
      'p>Please enter the confirmation code you received',
      {
        input:{
          id:'confirmationCode',
          placeholder:'(Enter code here)'
        }
      },
      {
        button:{
          innerHTML:'Confirm',
          onclick:function(){
            confirmAccount();
          },
          callback:function(){
            $(window).bind('keydown.confirmAccount',confirmAccount);
          }
        }
      }

    ).to(content);
}
function showSignUp(){
  const fields = 'username,email,familyName,personalName,sid,sid2,password,password2'.split(',');
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  elem.as(
    'div',
    'h2>Sign Up',
    'br',
    {
      label:{
        innerHTML:'Saitama University Email Address',
        htmlFor: 'username'
      }
    },
    'br',
    {
      input: {
        id: 'username',
        name: 'username',
        className: (tut ? 'blurred' : '')
      }
    },
    {
      select:{
        id:'email',
        name: 'email',
        callback:function(el){
          $(el).change(function(){
            if(this.value=='@mail'){
              $('.sid').hide();
            }
            else{
              $('.sid').show();
            }
          });
        }
      }
    },
    1,
    {
      option: {
        innerHTML: '@ms',
        value: '@ms'
      }
    },
    // {
    //   option:{
    //     innerHTML: '@mail',
    //     value: '@mail'
    //   }
    // },
    -1,
    'span'+(tut ? '.blurred' : '')+'>.university.ac.jp',
    'br',
    'br',
    {
      label:{
        innerHTML: 'Personal Name',
        htmlFor: 'personalName',
      }
    },
    'br',
    {
      input:{
        id:'personalName',
        name:'personalName',
        placeholder: 'Example: Taro'
      }
    },
    'br',
    'br',
    {
      label:{
        innerHTML: 'Family Name',
        htmlFor: 'familyName',
      }
    },
    'br',
    {
      input:{
        id:'familyName',
        name:'familyName',
        placeholder: 'Example: Suzuki'
      }
    },
    'br',
    'br',
    {
      label:{
        innerHTML: 'Student ID',
        htmlFor: 'sid',
        className: 'sid'
      }
    },
    'br',
    {
      input:{
        id:'sid',
        name:'sid',
        className: 'sid',
        placeholder: 'Example: 19zz123'
      }
    },
    'br',
    'br',
    {
      label:{
        innerHTML: 'Student ID (again)',
        htmlFor: 'sid2',
        className: 'sid'
      }
    },
    'br',
    {
      input:{
        id:'sid2',
        name:'sid2',
        className: 'sid',
        placeholder: 'Example: 19zz123'
      }
    },
    'br',
    'br',
    {
      label:{
        innerHTML: 'Password (at least 8 characters including both letters and numbers)',
        htmlFor: 'password'        }
    },
    'br',
    {
      input:{
        id:'password',
        name:'password',
        type: 'password'
      }
    },
    'br',
    'br',
    {
      label:{
        innerHTML: 'Password (again)',
        htmlFor: 'password2'        }
    },
    'br',
    {
      input:{
        id:'password2',
        name:'password2',
        type: 'password'
      }
    },

    'br',
    {
      button:{
        innerHTML: 'Sign Up',
        onclick:function(){
          const data = {};
          $('input,select').each(function(){
            data[this.id]=this.value && this.value.match(/\d/) && !this.value.match(/\D/) ? parseFloat(this.value) : this.value;
          });
          data.consent=1;
          send('signUp',data,showConfirmAccount);
        }
      }
    }
  ).to(content);
}
function showLogin(){
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  function logIn(keydownEvent=false){
    if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
      return;
    }
    if(!$('#loginBtn').length){
      $(window).unbind('keydown.login');
      return;
    }
    timeout(()=>{

      let username = $('#username').val().trim();
      username = username.indexOf('@') == -1 ? username : username.split('@')[0];
      const password = $('#password').val().trim()+'';



      send('logIn',{
          username: username,
          password: password
        },
        function(res){
          console.log('login successful');
          
          search('schedule');
            send('systemLogin',{username,password},function(res){
              console.log('systemLogin complete moving on' );
              elem.as({
                iframe:{
                  src:location.href.indexOf('su-apps') > -1 ? `https://su-apps.org/td/users/login?email=${username}&password=${password}` : `http://localhost:5000/users/login?email=${username}&password=${password}`,
                  style:{
                    display:'none'
                  }
                }
              }).to(document.body);
            },
            function(e){
              console.log('error');
            }
          );
          
        },
        function(){
          closeMessage();
          $('#username').val('');
          $('#password').val('');
        }
      );
    },200);
  }
  elem.as(
    'div',
//    'h2>Welcome to My Saitama University Courses Online',
    'h2>Log in',

    {
      input: {
        id: 'username',
        name: 'username',
        style:{width:'300px'},
        placeholder:'Username or Saidai Email',
        callback:function(el){
          $(el).focus();
        }
      }
    },
    'br',
    {
      input:{
        id:'password',
        name:'password',
        style:{width:'300px'},
        placeholder:'password',
        type: 'password'
      }
    },
    'br',
    {
      button:{
        id:'loginBtn',
        innerHTML:'Log in',
        onclick:function(){
          logIn();
        },
        callback:function(){
          $(window).bind('keydown.login',logIn);
        }
      },
    },
    'p',
    1,
    {
      span:{
        innerHTML:'Create an account',
        style:{marginRight:'20px'},
        className:'link smallPrint',
        onclick:function(){
          search('signup');
        }
      }
    },
    {
      span:{
        innerHTML:'Forgot my password...',
        className:'link smallPrint',
        onclick:function(){
          search('forgotPassword');
        }
      }
    },
    {
      a:{
        style:{marginLeft:'20px',textDecoration:'none'},
        innerHTML:'Contact Leander',
        className:'link smallPrint',
        href:'http://www.university.ac.jp/ceed/contactleander.htm'
      }
    }
  ).to(content);
}
function showSetNewPassword(){
  function setNewPassword(){
    const data = {};
    $('input').each(function(){data[this.id]=this.value;});
    send('setNewPassword',data,getSchedule);
  }
  setNewPassword = addOnEnter('setNewPassword',setNewPassword);
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  elem.as(
    'div',
    'h2>Set Your New Password',
    {
      input:{
        id:'password',
        className:'newPassword',
        placeholder:'New Password',
        type:'password'
      }
    },
    'br',
    {
      input:{
        id:'password2',
        className:'newPassword',
        placeholder:'New Password (again)',
        type:'password'
      }
    },
    'br',
    {
      button:{
        id:'setNewPassword',
        innerHTML:'Change my password',
        onclick:setNewPassword
      }
    },
    {
      button:{
        innerHTML:'Cancel',
        onclick:logOut
      }
    }
  ).to(content);
}
function showConfirmPasswordReset(res){
  function confirmReset(){
    const data = {};
    $('input').each(function(){data[this.id]=this.value;});
    send('confirmReset',data,()=>search('setNewPassword'));
  }
  confirmReset = addOnEnter('confirmReset',confirmReset);
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  elem.as(
    'div',
    'h2>Confirm Password Reset',
    'p>A confirmation code has been sent to <strong>'+res.to+'</strong> (make sure to check your spam folder if you don\'t see it).',
    "p>Please enter the code below, and click the 'Confirm Reset' button",
    {
      input:{
        id:'confirmationCode',
        placeholder:'(Enter code here)'
      }
    },
    'br',
    {
      button:{
        id:'confirmReset',
        innerHTML:'Confirm Reset',
        onclick:confirmReset
      }
    },
    {
      button:{
        innerHTML:'Cancel',
        onclick:function(){
          search('login');
        }
      }
    }
  ).to(content);
}
function addOnEnter(elId,func){
  function newFunc(keydownEvent=false){
    if(keydownEvent && keydownEvent.key && keydownEvent.key!='Enter'){
      return;
    }
    if(!$('#'+elId).length && $(window).unbind('keydown.'+elId)){
      return;
    }
    func();
  }
  $(window).bind('keydown.'+elId,newFunc);
  return newFunc;
}
function showPasswordReset(){
  function resetPassword(){
    const data = {};
    $('input,select').each(function(){data[this.id]=this.value});
    send('resetPassword',data,(res)=>showConfirmPasswordReset(res));
  }
  resetPassword = addOnEnter('resetPassword',resetPassword);
  elem.clear(content);
  elem.clear(header);
  elem.as(topBar()).to(header);
  elem.as(
    'div',
    'h2>Password Reset',
    "p>To reset your password, enter your Saitama University email below and click the 'Reset Password' button.",
    'p',
    1,
    {
      input:{
        placeholder:'(username)',
        id:'username'
      }
    },
    {
      select:{
        id:'email'
      }
    },
    1,
    {
      option:{
        innerHTML:'@ms',
        value:'@ms'
      }
    },
    // {
    //   option:{
    //     innerHTML:'@mail',
    //     value:'@mail'
    //   }
    // },
    -1,
    'span>.university.ac.jp',
    -1,
    {
      button:{
        id:'resetPassword',
        innerHTML:'Reset Password',
        onclick:resetPassword
      }
    },
    {
      button:{
        innerHTML:'Cancel',
        onclick:function(){
          search('login');
        }
      }
    }
  ).to(content);
}
function logOut(){
  send('logOut',()=>elem.clear(document.body) && window.location.reload());
}
function getSchedule(){
  send('getSchedule',showSchedule);
}
function getConfirmMail(){
  send('getConfirmMail',showConfirmAccount);
}
function getLoginStatus(){
  send('getLoginStatus',function(res){
    if(res.loggedIn){

      console.log('res after getLoginStatus');
      search('schedule');
    }
    else if(res.confirmRequired){
      search('confirm');
    }
    else{
      showLogin();
    }
  });
}
function init(){
  const act = {
    login:getLoginStatus,
    logout:logOut,
    signup:showSignUp,
    //schedule:getSchedule,
    schedule:dash,
    dash:dash,
    confirm:getConfirmMail,
    forgotPassword:showPasswordReset,
    confirmPasswordReset:showConfirmPasswordReset,
    setNewPassword:showSetNewPassword,
    showSurvey:showSurvey,
    showGrades:showGrades
  };
  const search = window.location.href.split('#')[1];
  act[search] ? act[search]() : getLoginStatus();
}
init();
