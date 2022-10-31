if(!elem){
  throw('elem.js is required for showMessage.js to work');
}
function closeMessage(callback){
  $('#mask,#message').remove();
  callback && typeof callback=='function' && callback();
  return true;
}
function showMessage(...args){
  const message = args.shift();
  let callback = args.length && typeof args[0] == 'function' ? args.shift() : false;
  function callback2(keydownEvent=false){
    if(keydownEvent && keydownEvent.key && keydownEvent.key !='Enter'){
      return;
    }
    $(window).unbind('keydown.message');
    closeMessage(callback);
  }
  !args.length && $(window).bind('keydown.message',callback2);
  !args.length && args.push({button:{innerHTML:'OK',onclick:callback2}});
  args = args.length == 1 && Array.isArray(args[0]) ? args[0] : args;
  $('#maskContainer,#mask,#message').remove();
  let clickedOnMessage = 0;
  elem.as(
    {
      div:{
        id:'maskContainer'//,
        // style:{
        //   position:'absolute',
        //   left:0,
        //   top:0,
        //   width:window.innerWidth+'px',
        //   height:'10px'
        // }
      }
    },
    1,
    {
      div:{
        id:'mask',
        style:{
          position: 'fixed',
          zIndex: 1000,
          top: 0,
          bottom: 0,
          left: 0,
          right: 0,
          backgroundColor: 'rgba(255,255,255,.7)'
        },
        onclick:function(){
          if(new Date().getTime()-clickedOnMessage < 10){
            return;
          }
          callback2();
        }
      }
    },
    1,
    {
      div:{
        id:'message',
        style:{
          position:'relative',
          margin: Math.floor((window.innerHeight)*.1)+'px auto',
          width: '600px',
          zIndex: '1500',
          display: 'block',
          textAlign: 'center',
          backgroundColor: 'rgba(255,255,255,1)',
          padding: '25px 25px 25px 25px',
          border: '1px solid gray',
          maxHeight:Math.floor((window.innerHeight)*.7)+'px',
          overflowY:'scroll'
        },
        onclick:function(){
          clickedOnMessage = new Date().getTime();
        }
      }
    },
    1,
    {
      div:{
        innerHTML: message,
        style:{
          textAlign: message.length < 70 ? 'center' : 'left'
        }
      }
    },
    ...args,
    -1,
  -1
  ).to(document.body);
  return true;
}


function showInSubWindow(el,els){
  console.log(el);
  const elTop = el.getBoundingClientRect().y + window.pageYOffset;
  console.log(elTop);

  const mask = elem.as(
    {
      div:{
        id:'subWindowMask',
        style:{
          position:'fixed',
          left:0,
          right:0,
          top:0,
          bottom:0,
          backgroundColor: 'rgba(255,255,255,.7)',
          zIndex: 2,
          display:'block'
        }
      }
    }
  ).to(document.body);

  !els.length && els.push('div');
  let timeMessageClicked = 0;
  const container = elem.as([
    {
      div:{
        id:'subWindowContainer',
        style:{
          position:'absolute',
          width:'100%',
          top: 0,
          left: 0,
          zIndex:9999
        }
      }
    },
    {
      div:{
        id:'subWindowMessage',
        style:{
          position:'relative',
          backgroundColor:'white',
          border: '1px solid gray',
          margin:`${elTop}px auto`,
          width:'80%',
          minHeight:'200px',
          display:'block',
          padding:'25px 25px 25px 25px'
        },
        onclick:function(){
          console.log('mb clicked');
          timeMessageClicked = new Date().getTime();
        },
        callback:function(el){
          function closeSubWindow(){
            if(new Date().getTime() - timeMessageClicked < 10){
              console.log(new Date().getTime() - timeMessageClicked);
              return;
            }
            $('#subWindowMask,#subWindowContainer').remove();
            window.removeEventListener('click',closeSubWindow);
          }
          window.addEventListener('click',closeSubWindow);
        }
      }
    },
    1,
    ...els,
    -1
  ]).to(document.body);

  return true;
}
