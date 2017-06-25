require 'travis'

SCHEDULER.every('15s', first_in: '1s') {
  client = Travis::Client.new
  repo  = client.repo("TransformCore/beis-par-beta")

  build = repo.branch('master')
  
  if build.green?
    health = 'ok'
  elsif build.yellow?
    health = 'warning'
  else
    health = 'critical'
  end

  info = "[#{build.branch_info}]"
  number = build.number

  send_event('master_build_status', { status: health })
  send_event('master_build_version', { text: "##{number}" })

  client.clear_cache
}