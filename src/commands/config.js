import { ServerConfig, DSMConfig } from '../lib/MainConfig'
import colors from 'colors'

// Command Name *required
export const command = "config"

// Command Alias
export const alias = ""

// Command Options
export const options = [
  {flag:'-s, --set <value>', description:'Sets the config option'},
  {flag:'-c, --config <name>', description:'gets the specified configs value'}
]

// Command Description *required
export const description = "displays or sets config values"

// Command Action *required
export const action = (options, galaxy)=>{
  if(options.set && !options.config){
    console.log('usage: dsm config -c MOTD -s "My new motd text"')
    return
  }
  let ConfigToShow
  if(galaxy){
    ConfigToShow = new ServerConfig(galaxy.name)
  }else{
    ConfigToShow = new DSMConfig() 
  }
  
  console.log(colors.blue('------ Config ------'))
  const ConfigNames = Object.keys(Config)
  if(options.config){
    if(ConfigNames.indexOf(options.config) > -1){
      if(options.set){
        Config[options.config].value = options.set
        console.log('Set config option '+colors.green(options.config)+' to:')
        console.log('   '+ options.set)
      }else{
        DisplayConfig(options.config)
      }
    }else{
      console.log(colors.red('No Config option: ')+options.config)
    }
    return
  }
  ConfigNames.map(opt=>{
    DisplayConfig(opt)
  })
}

const DisplayConfig = (opt) => {
  console.log(colors.green(opt+' - '))
  console.log('    '+Config[opt].description)
  console.log('    Type: '+Config[opt].type)
  console.log('    Default: '+Config[opt].default)
  console.log('    Current: '+Config[opt].value)
}