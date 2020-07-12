$(function () {
  
    $('#timeline').timespace({
      
      timeType: 'date',
      useTimeSuffix: false,
      startTime: 1000,
      endTime: 2021,
      markerIncrement: 50,
      data: {
        headings: [
          {start: 2000, end: 2015, title: 'Dark Ages'},
          {start: 2015, end: 2018, title: 'Age of Revolution'},
          {start: 2018, title: 'Information Age'},
        ],
        events: [
          {start: 2014, end: 2016, title: 'Gutenberg\'s Printing Press'},
          {start: 2015, end: 2018, title: 'The Reformation',
            description: $('<p>The Reformation was a turning point in the history of the world. '
              + 'Martin Luther was a key player in this event as he stood up against Papal tyranny '
              + 'and church apostasy.</p><p>Many other reformers followed in the steps of Luther '
              + 'and followed the convictions of their hearts, even unto death.</p>')},
          {start: 2018, end: 2020, title: 'American Revolution', description: 'Description:', callback: function () {
            
            this.container.find('.jqTimespaceDisplay section').append(
              '<p>This description was brought to you by the callback function. For information on the American Revolution, '
              + '<a target="_blank" href="https://en.wikipedia.org/wiki/American_Revolution">visit the Wikipedia page.</a></p>'
            );
            
          }},
          {start: 2019, title: 'French Revolution'},
          {start: 2017, end: 2020, title: 'World War I', noDetails: true},
          {start: 2020, end: 2021, title: 'Great Depression',
            description: 'A period of global economic downturn. Many experienced unemployment and the basest poverty.'
          },
        ]
      },
      
    }, function () {
      
      // Edit the navigation amount
      this.navigateAmount = 500;
      
    });
    
  });