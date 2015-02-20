import dbClass
import factoryClasses

create dbObject
create factoryObjects passing dbObject

parse request url into controller/action/params

import controllerClass
create controllerObject passing dbObject, factoryObjects
call controllerObject->action(params)
