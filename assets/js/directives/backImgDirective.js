/**
 * Created by Personne on 13/10/2015.
 */
app.directive('backImg', function(){

    return function(scope, element, attrs) {
        scope.$watch(attrs.backImg, function(value) {
            var image = new Image();
            image.src = value;
            image.onload = function(){
                element.css({
                    'background-image': 'url(' + value +')',
                    'background-size' : 'cover'
                });
            };
        });
    };
});