<script>
    // Rendedizador HTML dinámico
    function htmlRender(){
        this.elementId = 0;
        this.serialChar = "-";
        this.firstLoad = true;
        this.submitedYet = false;

        // Doc para funcion addElement
        this.serializeMany = function (element)
        {
            for (var i = 0; i <= element.length - 1; i++) {
                element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;
                this.elementId += 1;
       
            };
            return(element);
        };

        // Doc para funcion addElement
        this.serialize = function (element)
        {        
            element["id"] = element["id"] + this.serialChar + this.elementId;
            this.elementId += 1;
            return(element);
        };

        // Documentacion para metodo serializeMatch
        this.serializeMatch = function(element){
            this.elementId += 1;
            for (var i = 0; i <= element.length - 1; i++) {
                element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;   
            };
            return(element);
        };

        // Documentacion para metodo serializeNodes
        this.serializeNodes = function(element){
            for (var i = 0; i <= element.length - 1; i++) {
                element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;   
            };
            return(element);
        };

        // Documentacion para metodo serializeNode
        this.serializeNode = function(element){
            element["id"] = element["id"] + this.serialChar + this.elementId;   
            return(element);
        };

        // Documentacion para metodo serializeWith
        this.serializesWith = function(id, element){
            for (var i = 0; i <= element.length - 1; i++) {
                element[i]["id"] = element[i]["id"] + this.serialChar + id;
                console.log((element[i]["id"] + this.serialChar + id));
            };
            return(element);
        };

            // Documentacion para metodo serializeWith
        this.serializeWith = function(id, element){
            element["id"] = element["id"] + this.serialChar + id;   
            return(element);
        };

        // Documentacion para metodo serializeUpp
        this.serializeUpp = function(){
            this.elementId += 1;
        };
        
        // Documentacion para metodo option
        this.option = function(id, content, value, eventValues){
            var temp = {
                "id": id,
                "type": "option",
                "content": content,
                "attrs":{"value":value}
            };

           for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo select
        this.select = function(id, name, eventValues, optionContent){
            var temp = {
                    "type": "select",
                    "id": id,
                    "attrs":{
                        "name":name
                        },
                    "content": optionContent 
                    };
            
            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };


            return(temp);
        };

        // Documentacion para metodo div
        this.div = function(id, content, eventValues){
            var temp = {
                    "type": "div",
                    "id": id,
                    "content": content,
                    "attrs":{}
            };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };


        // Documentacion para metodo inputText
        this.inputText = function(id, cssClass, name, value, eventValues){
            var temp = {
                    "id": id,
                    "css": cssClass,
                    "type": "input",
                    "attrs":{
                            "name":name,
                            "value": value
                        }
                    };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo tr
        this.tr = function(id, eventValues, content){
            var temp = {
                    "id": id,
                    "type": "tr",
                    "content": content,
                    "attrs":{}
                    };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo tr
        this.td = function(id, eventValues, content){
            var temp = {
                    "id": id,
                    "type": "td",
                    "content": content,
                    "attrs":{}
                    };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo tdMany
        this.tdMany = function(elements, eventValues, id){
            var temp = new Array();
            for (var i = 0; i < elements.length; i++) {
                temp.push(this.td(id, eventValues, elements[i]));
            };
            return(temp);
        };
      

        // Doc para funcion createElement
        this.createElement = function (dictElement)
        {
            return(fastFrag.create(dictElement));
        };

        // Doc para funcion addField
        this.pushExternalField = function (stack, element)
        {
            document.getElementById(stack).appendChild(element);
            
        };

        // Documentacion para metodo replace
        this.replace = function(stack, element){
            var upperElement = document.getElementById(stack);
            var childs = upperElement.childNodes;
            if (childs.length < 1) {
                this.pushExternalField(stack, element);
            } else{
                while (upperElement.firstChild) {
                    console.log(upperElement.firstChild);
                    upperElement.removeChild(upperElement.firstChild);

                };
                this.pushExternalField(stack, element);    
            };
                  
        };

        // Documentacion para metodo getId
        this.getId = function(stringValue){
            var elementId = stringValue.split(this.serialChar);
            return(elementId[(elementId.length -1)]);
        };

        // Documentacion para metodo loaded
        this.loaded = function(){
            this.firstLoad = false;
        };

        // Documentacion para metodo submitted
        this.submited = function(){
            this.submitedYet = true;
        };

        // Documentacion para metodo submitted
        this.isSubmited = function(){
            return(this.submitedYet);
        };        

        // Documentacion para metodo button
        this.button = function(id, value, eventValues){
            var temp = {
                    "id": id,
                    "type": "input",
                    "attrs":{
                        "type":"button",
                        "value":value
                        }
                    };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo button
        this.submitButton = function(id, value, eventValues){
            var temp = {
                    "id": id,
                    "type": "button",
                    "content":value,
                    "attrs":{}
                    };

            for(var prop in eventValues){
                temp["attrs"][prop] = eventValues[prop]+"(this);";
            };

            return(temp);
        };

        // Documentacion para metodo hidden
        this.hidden = function(id, name, value){
            var temp = {
                "id": id,
                "type": "hidden",
                "content":value,
                "attrs":{
                    "name":name,
                    "value":value
                }
            };
            return(temp);
        };

    }
</script>