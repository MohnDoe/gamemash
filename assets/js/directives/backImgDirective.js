/**
 * Created by Personne on 13/10/2015.
 */
app.directive('backImg', function(){

    return function(scope, element, attrs) {
        scope.$watch(attrs.backImg, function(value) {
            console.log('clearing lol');
            element.css({
                'background': 'none !important',
                'display' : 'none'
            });

            var image = new Image();
            image.src = value;
            image.onload = function(){
                element.css({
                    'background-image': 'url(' + value +')',
                    'background-size' : 'cover',
                    'display' : 'block'
                });
            };
        });
    };
});