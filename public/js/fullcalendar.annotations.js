function renderAnnotations(view,annotations) {
  var container = $(view.element).find('.fc-agenda-slots').parent();
  if ( container.find('#annotationSegmentContainer').length === 0 ) {
    annotationSegmentContainer = $("<div style='position:absolute;z-index:-1;top:0;left:0' id='annotationSegmentContainer'>").prependTo( container );
  }
  else {
    annotationSegmentContainer = container.find('#annotationSegmentContainer');
  }

  var html = '';
  for (var i=0; i < annotations.length; i++) {
    var ann = annotations[i];
    if (ann.start >= view.start && ann.end <= view.end) {
      var top = view.timePosition(ann.start, ann.start);
      var bottom = view.timePosition(ann.end, ann.end);
      var height = bottom - top;
      var dayIndex = $.fullCalendar.dayDiff(ann.start, view.visStart);

      var left = view.colContentLeft(dayIndex) - 2;
      var right = view.colContentRight(dayIndex) + 3;
      var width = right - left;

      var cls = '';
      if (ann.cls) {
        cls = ' ' + ann.cls;
      }

      var colors = '';
      if (ann.color) {
        colors = 'color:' + ann.color + ';';
      }
      if (ann.background) {
        colors += 'background:' + ann.background + ';';
      }

      var body = ann.title || '';

      html += '<div style="position: absolute; ' +
        'top: ' + top + 'px; ' +
        'left: ' + left + 'px; ' +
        'width: ' + width + 'px; ' +
        'height: ' + height + 'px;' + colors + '" ' +
        'class="fc-annotation fc-annotation-skin' + cls + '">' +
        body +
        '</div>';
    }
  }
  annotationSegmentContainer.html(html);
}